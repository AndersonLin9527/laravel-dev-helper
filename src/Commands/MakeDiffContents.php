<?php

namespace Anderson9527\LaravelDevHelper\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class MakeDiffContents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'x-anderson9527:make-diff-contents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'v12.0.3 Make Diff Contents for ChatGPT';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws Exception
     * @example php artisan x-anderson9527:make-diff-contents
     */
    public function handle(): int
    {
        // 檢查是否為 Git 專案
        exec('git rev-parse --is-inside-work-tree 2> /dev/null', $gitCheckOutput, $gitCheckCode);
        if ($gitCheckCode !== 0 || trim($gitCheckOutput[0] ?? '') !== 'true') {
            $this->error('Sorry, This Command REQUIRED the project to be a Git repository.');
            return SymfonyCommand::FAILURE;
        }

        // 取得異動的檔案列表
        $output = null;
        $resultCode = null;
        exec('git status --short', $output, $resultCode);

        if ($resultCode !== 0 || empty($output)) {
            $this->error('Failed to get git status or no changes detected.');
            return SymfonyCommand::FAILURE;
        }
//        dump($output);

        // 確保輸出目錄存在
//        $outputDirectory = base_path('app/Console/Commands/Helper/ChatGPT/files');
        $outputDirectory = storage_path('app/x-anderson9527/laravel-dev-helper/MakeDiffContents');
        if (!File::exists($outputDirectory)) {
            File::makeDirectory($outputDirectory, 0755, true);
        }

        // 初始化輸出檔案路徑
        $outputFile = $outputDirectory . '/update_contents.md';
        // 取得 Config
        $localConfig = $this->loadLocalConfig();

        // 生成更新內容
        $updateContents = [];
        $updateContents[] = $localConfig['prefixText'];
        $updateContents[] = '';

        foreach ($output as $outputLine) {
            // 解析 Git 狀態輸出格式 (例如: "M filepath" or "A filepath")
            $outputLine = trim($outputLine);
            $outputLine = explode(' ', $outputLine);
            $fileGitStatus = reset($outputLine);
            $fileGitStatusText = $this->getFileGitStatus($fileGitStatus);
            $filePath = last($outputLine);
            $absoluteFilePath = base_path($filePath);
            $relativeFilePath = str_replace(base_path(), '', $absoluteFilePath);
            $absoluteFileDir = dirname($absoluteFilePath);
            $relativeFileDir = str_replace(base_path(), '', $absoluteFileDir);

//            $this->line('$fileGitStatusText   : ' . $fileGitStatusText);
//            $this->line('$absoluteFileDir     : ' . $absoluteFileDir);
//            $this->line('$relativeFileDir     : ' . $relativeFileDir);
//            $this->line('$absoluteFilePath    : ' . $absoluteFilePath);
//            $this->line('$relativeFilePath    : ' . $relativeFilePath);
//            $this->line('--------------------------------------------------');

            foreach ($localConfig['ignoreDirs'] as $ignoreDir) {
                if (str_starts_with($relativeFileDir, $ignoreDir)) {
                    $this->warn('Ignore dir  : ' . $relativeFileDir);
                    continue 2;
                }
            }

            if (in_array($relativeFilePath, $localConfig['ignoreFiles'])) {
                $this->warn('Ignore file : ' . $relativeFilePath);
                continue;
            }

            $updateContents[] = $fileGitStatusText . ': ' . $filePath;
            // 檢查檔案是否存在於檔案系統上 (可能已刪除等原因)
            if (File::exists($absoluteFilePath)) {
                $fileContent = File::get($absoluteFilePath);
                $updateContents[] = '';
                $updateContents[] = "```" . $this->getFileSyntaxHighlight($filePath);
                $updateContents[] = $fileContent;
                $updateContents[] = "```";
            }
            $updateContents[] = '';
            $updateContents[] = '--------------------------------------------';
            $updateContents[] = '';
        }

        if ($localConfig['suffixText'] != '') {
            $updateContents[] = $localConfig['suffixText'];
        }

        // 寫入檔案
        File::put($outputFile, implode(PHP_EOL, $updateContents));

        $this->info("Updated content written to $outputFile");
        return SymfonyCommand::SUCCESS;
    }

    /**
     * 取得檔案 git 狀態
     * @param string $fileGitStatus
     * @return string
     * @throws Exception
     */
    private function getFileGitStatus(string $fileGitStatus): string
    {
        return match ($fileGitStatus) {
            '??', 'A' => '新增',
            'AM' => '新增+修改',
            'D' => '刪除',
            'M', 'MM' => '修改',
            'R' => '重新命名',
            'RM' => '重新命名+修改',
            default => throw new Exception('Cannot parse file git status: ' . $fileGitStatus), // 預設為純文字
        };
    }

    /**
     * 根據檔案副檔名，返回對應的語法高亮類型 (用於 Markdown).
     *
     * @param string $filePath
     * @return string
     */
    private function getFileSyntaxHighlight(string $filePath): string
    {
        $extension = File::extension($filePath);

        return match ($extension) {
            'php' => 'php',
            'js' => 'javascript',
            'css' => 'css',
            'html' => 'html',
            'json' => 'json',
            'md' => 'markdown',
            'sh' => 'bash',
            default => 'plain', // 預設為純文字
        };
    }

    /**
     * 讀取專案根目錄的本機設定檔
     * .local-anderson9527-laravel-dev-helper.php
     *
     * @return array{ignoreDirs: array<int,string>, ignoreFiles: array<int,string>}
     */
    private function loadLocalConfig(): array
    {
        $path = base_path('.local-anderson9527-laravel-dev-helper.php');

        if (!file_exists($path)) {
            return [
                'ignoreDirs' => [],
                'ignoreFiles' => [],
            ];
        }

        $config = include $path;

        // 安全地取出對應的 key
        $config = is_array($config) ? $config : [];
        $makeDiffConfig = $config['MakeDiffContents'] ?? [];

        return [
            'ignoreDirs' => array_values(array_filter(
                $makeDiffConfig['ignore_dirs'] ?? [],
                fn($v) => is_dir(base_path($v)) && $v !== ''
            )),
            'ignoreFiles' => array_values(array_filter(
                $makeDiffConfig['ignore_files'] ?? [],
                fn($v) => is_string($v) && $v !== ''
            )),
            'prefixText' => $makeDiffConfig['prefix_text'] ?? '',
            'suffixText' => $makeDiffConfig['suffix_text'] ?? '',
        ];
    }


}
