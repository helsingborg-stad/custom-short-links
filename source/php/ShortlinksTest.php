<?php

declare(strict_types=1);

namespace CustomShortLinks;

use PHPUnit\Framework\TestCase;
use RuntimeException;

final class TranslationSpy
{
    public static string $message = '';
    public static string $domain = '';
}

function __(string $message, string $domain = ''): string
{
    TranslationSpy::$message = $message;
    TranslationSpy::$domain = $domain;

    return $message;
}

function wp_die(string $message): never
{
    throw new WpDieException($message);
}

/**
 * @internal
 */
final class WpDieException extends RuntimeException
{
}

/**
 * @internal
 */
final class TestableShortlinks extends Shortlinks
{
    /**
     * Intentionally skips the parent constructor to avoid registering WordPress hooks in unit tests.
     */
    public function __construct(
        private readonly ?object $existingPost = null,
    ) {
    }

    protected function getPageByTitle(string $title, string $postType = 'page')
    {
        return $this->existingPost;
    }
}

/**
 * @internal
 */
final class ShortlinksTest extends TestCase
{
    protected function setUp(): void
    {
        TranslationSpy::$message = '';
        TranslationSpy::$domain = '';
    }

    protected function tearDown(): void
    {
        TranslationSpy::$message = '';
        TranslationSpy::$domain = '';
    }

    public function testSanitizeTitleTrimsSlashesForShortlinks(): void
    {
        $shortlinks = new TestableShortlinks();

        $result = $shortlinks->sanitizeTitle(
            array(
                'post_type' => 'custom-short-link',
                'post_title' => '/my-link/',
            ),
            array(),
        );

        $this->assertSame('my-link', $result['post_title']);
    }

    public function testSanitizeTitleThrowsWhenAnotherShortlinkUsesTheSameTitle(): void
    {
        $shortlinks = new TestableShortlinks((object) array('ID' => 12));

        try {
            $shortlinks->sanitizeTitle(
                array(
                    'post_type' => 'custom-short-link',
                    'post_title' => '/my-link/',
                ),
                array(
                    'ID' => 34,
                ),
            );

            $this->fail('Expected duplicate shortlinks to trigger wp_die().');
        } catch (WpDieException $exception) {
            $this->assertSame('Short links must be unique.', $exception->getMessage());
            $this->assertSame('Short links must be unique.', TranslationSpy::$message);
            $this->assertSame('custom-short-links', TranslationSpy::$domain);
        }
    }

    public function testSanitizeTitleAllowsUpdatingTheExistingShortlinkWithTheSameTitle(): void
    {
        $shortlinks = new TestableShortlinks((object) array('ID' => 12));

        $result = $shortlinks->sanitizeTitle(
            array(
                'post_type' => 'custom-short-link',
                'post_title' => '/my-link/',
            ),
            array(
                'ID' => 12,
            ),
        );

        $this->assertSame('my-link', $result['post_title']);
    }

    public function testSanitizeTitleSkipsDuplicateCheckWhenTheSanitizedTitleIsEmpty(): void
    {
        $shortlinks = new TestableShortlinks((object) array('ID' => 12));

        $result = $shortlinks->sanitizeTitle(
            array(
                'post_type' => 'custom-short-link',
                'post_title' => '///',
            ),
            array(
                'ID' => 34,
            ),
        );

        $this->assertSame('', $result['post_title']);
        $this->assertSame('', TranslationSpy::$message);
        $this->assertSame('', TranslationSpy::$domain);
    }
}
