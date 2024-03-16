<?php

declare (strict_types = 1);

namespace J7\WpToolkit;

final class Debug
{

    public function __construct()
    {
        \add_action('admin_menu', [ $this, 'register_submenu_page' ]);
    }

    private function read_debug_log()
    {
        $log_path = WP_CONTENT_DIR . '/debug.log'; // 使用 WP_CONTENT_DIR 常量定义日志文件路径
        if (file_exists($log_path)) { // 检查文件是否存在
            $lines       = file($log_path); // 读取文件到数组中，每行是一个数组元素
            $lastLines   = array_slice($lines, -300); // 获取最后50行
            $log_content = implode("", $lastLines); // 将数组元素合并成字符串
            if ($log_content === false) {
                // 处理读取错误
                return 'Error reading log file.';
            }
            return nl2br(esc_html($log_content)); // 将换行符转换为HTML换行，并转义内容以避免XSS攻击
        } else {
            return 'Log file does not exist.';
        }
    }

    public function register_submenu_page()
    {
        \add_submenu_page(
            'tools.php', // 父菜单文件，指向工具菜单
            'Debug Log Viewer', // 页面标题
            'Debug Log', // 菜单标题
            'manage_options', // 所需的权限，例如管理选项
            'debug-log-viewer', // 菜单slug
            [ $this, 'debug_log_page_content' ], // 用于渲染页面内容的回调函数
            1000
        );
    }
    public function debug_log_page_content()
    {
        // 这里是渲染内容的函数，可以调用之前创建的 read_debug_log() 函数
        echo '<div class="wrap"><h1>Debug Log</h1>';
        echo '<p>只顯示 <code>/wp-content/debug.log</code> 最後 300 行</p>';
        echo '<pre style="line-height: 0.75;">' . $this->read_debug_log() . '</pre></div>'; // 使用 <pre> 标签格式化文本输出
    }
}

new Debug();
