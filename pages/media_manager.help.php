<?php
$file = rex_file::get(rex_path::addon('media_manager_plus', 'README.md'));
$body = rex_markdown::factory()->parse($file);
$fragment = new rex_fragment();
$fragment->setVar('body', $body, false);
$content = $fragment->parse('core/page/section.php');
echo $content;
?>