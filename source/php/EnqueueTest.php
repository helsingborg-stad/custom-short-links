<?php

declare(strict_types=1);

use CustomShortLinks\Enqueue;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\NativeWpService;
use WpUtilService\WpUtilService;

final class EnqueueTest extends TestCase
{
    public static array $hooks = [];
    public static string $dequeued = '';
    public static array $registered = [];
    public static string $enqueued = '';
    public static array $localized = [];

    #[RunInSeparateProcess]
    public function testNativeServiceRegistersAndLocalizesShortlinkScript(): void
    {
        function add_action($hook, $callback, $priority = 10, $acceptedArgs = 1): bool
        {
            EnqueueTest::$hooks[$hook] = $callback;
            return true;
        }

        function get_current_screen(): object
        {
            return (object) ['post_type' => 'custom-short-link', 'action' => 'add'];
        }

        function wp_dequeue_script($handle): void
        {
            EnqueueTest::$dequeued = $handle;
        }

        function plugins_url($path = '', $plugin = ''): string
        {
            return 'https://example.test/wp-content/plugins/custom-short-links';
        }

        function wp_cache_get($key, $group = '', $force = false, &$found = null): array
        {
            $found = true;
            return [];
        }

        function wp_register_script($handle, $src, $deps = [], $ver = false, $args = []): bool
        {
            EnqueueTest::$registered = [$handle, $src, $deps, $ver, $args];
            return true;
        }

        function wp_enqueue_script($handle, $src = '', $deps = [], $ver = false, $args = []): void
        {
            EnqueueTest::$enqueued = $handle;
        }

        function wp_localize_script($handle, $objectName, $data): bool
        {
            EnqueueTest::$localized = [$handle, $objectName, $data];
            return true;
        }

        function home_url(): string
        {
            return 'https://example.test';
        }

        function __($message, $domain = ''): string
        {
            return $message;
        }

        $service = new WpUtilService(new NativeWpService());
        new Enqueue($service->enqueue('/var/www/wp-content/plugins/custom-short-links'));
        (self::$hooks['admin_enqueue_scripts'])();

        static::assertSame('autosave', self::$dequeued);
        static::assertSame(
            ['js-custom-short-linksjs', 'https://example.test/wp-content/plugins/custom-short-links/assets/dist/js/custom-short-links.js', [], false, true],
            self::$registered,
        );
        static::assertSame('js-custom-short-linksjs', self::$enqueued);
        static::assertSame(
            ['js-custom-short-linksjs', 'CustomShortLinksVars', ['home_url' => 'https://example.test', 'shortlink' => 'Shortlink']],
            self::$localized,
        );
    }
}
