我這個套件是輔助 dev 開發沒錯

所以安裝指令是 `composer require --dev anderson9527/laravel-dev-helper:^12`

有特別 + --dev

我現在希望安裝此套件的人 可以多一個 config 可以控制

src/Commands/MakeDiffContents.php 中的

$ignoreDirs

$ignoreFiles

目前的想法是多一個 publish 命令

但是又不想被 git，請給我專業建議
