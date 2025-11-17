<?php

/**
 * -----------------------------------------------------------------------------
 * Anderson9527 Laravel Developer Configuration (Not for Version Control)
 * 本機開發者專用設定（不建議加入版控）
 * -----------------------------------------------------------------------------
 *
 * This file allows each developer to customize how MakeDiffContents ignores
 * specific directories or files in their local environment.
 *
 * 此檔案讓每位開發者可以自訂 MakeDiffContents 在本機環境中
 * 要忽略哪些目錄或檔案。
 *
 * NOTICE:
 * - It is recommended to add this file to your project’s `.gitignore` so that
 *   individual preferences do not affect other team members.
 * - 建議將此檔案加入 `.gitignore`，以避免個人偏好影響到其他開發者。
 *
 * Example .gitignore entry:
 *   .local-anderson9527-laravel-dev-helper.php
 */

return [

    'MakeDiffContents' => [

        /**
         * ---------------------------------------------------------------------
         * ignore_dirs
         * ---------------------------------------------------------------------
         * Directories to ignore, relative to the project root.
         * Must begin with a leading slash "/" for consistency.
         *
         * 需要被忽略的目錄（相對於專案根目錄）
         * 必須以 "/" 開頭以確保比對正確。
         *
         * Examples:
         *   '/app/Console',
         *   '/public',
         */
        'ignore_dirs' => [
        ],

        /**
         * ---------------------------------------------------------------------
         * ignore_files
         * ---------------------------------------------------------------------
         * Specific files to ignore, relative to the project root.
         * Must begin with a leading slash "/" for consistency.
         *
         * 需要被忽略的指定檔案（相對於專案根目錄）。
         * 必須以 "/" 開頭以確保比對正確。
         *
         * Examples:
         *   '/app/Http/Controllers/Controller.php',
         *   '/backup/composer/composer-with-local-packages.json',
         *   '/composer.lock',
         */
        'ignore_files' => [
            '/composer.lock',
        ],

        /**
         * ---------------------------------------------------------------------
         * prefix_text
         * ---------------------------------------------------------------------
         * Text to be inserted at the beginning of the generated output file.
         * Useful for adding a header, timestamp, or introductory description.
         *
         * 產生輸出內容時，會自動插入於最開頭的文字。
         * 可用於加入標題、時間戳記、或任何開頭描述。
         *
         * Notes:
         * - This supports PHP expressions. For example, using `date()` will
         *   generate the timestamp at execution time.
         * - 可使用 PHP 表達式，例如 `date()`，文字會在命令執行時產生。
         *
         * Example:
         *   '以下是我 ' . date('Y-m-d H:i:s') . ' 修改的檔案及內容：',
         */
        'prefix_text' => '以下是我 ' . date('Y-m-d H:i:s') . ' 修改的檔案及內容：',

        /**
         * ---------------------------------------------------------------------
         * suffix_text
         * ---------------------------------------------------------------------
         * Text to be appended after each file’s diff content block.
         * Can be used for separators, formatting markers, or custom notes.
         *
         * 會附加在每個檔案內容區塊後方的文字。
         * 可用於加上區隔線、格式標示、或自定義註解。
         *
         * Example:
         *   '--------------------------------------------'
         *   或留空表示不加入任何內容。
         */
        'suffix_text' => '',
    ],

];
