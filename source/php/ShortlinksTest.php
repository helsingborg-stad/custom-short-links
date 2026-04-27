<?php

declare(strict_types=1);

namespace CustomShortLinks;

use PHPUnit\Framework\TestCase;
use RuntimeException;

function __(string $message, string $domain = ''): string
{
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

        $this->expectException(WpDieException::class);
        $this->expectExceptionMessage('Short links must be unique.');

        $shortlinks->sanitizeTitle(
            array(
                'post_type' => 'custom-short-link',
                'post_title' => '/my-link/',
            ),
            array(
                'ID' => 34,
            ),
        );
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
}
