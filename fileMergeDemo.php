<?php 

$param = $argv;
$scriptName = __FILE__;

if (count($param) < 2) {
	echo "Usage: php current_file target_dir [, suffix]\n";
	exit;
}

// 格式化路径目录
$dirPath = $param[1];
if (!is_dir($dirPath)) {
	$dirPath = dirname(__FILE__).'/'.$dirPath;
	if (!is_dir($dirPath)) {
		echo "illegal dir name!\n";
		exit;
	}
}


// 是否指定合并的后缀文件名
$suffix = '';
if (isset($param[2])) {
	$suffix = $param[2];
} else {
	echo 'There is no suffix specified, default is "txt"'."\n";
	$suffix = 'txt';
}

// 合并文件的保存路径
$distPath = './';
if (isset($param[3])) {
	$distPath = $param[3];
	if (!is_dir($distPath)) {
		if (!mkdir($distPath, 0775, true)) {
			echo "could not add the distination path\n";
			exit;
		}
	}
}

$resName = date('Ymdhis').'.'.$suffix;
$distFullname = rtrim($distPath, '/').'/'.$resName;

function comment($content, $suffix)
{
	$comment = array(
		'sql' => '-- {content}',
		// 'php' => '/* {content} */',
		'ini' => '; {content}',
		'html' => '<!-- {content} -->',
		'js' => '/* {content} */',
		'css' => '/* {content} */',
		'default' => '// {content}',
	);
	if (!array_key_exists($suffix, $comment)) {
		$suffix = 'default';
	}
	return str_replace('{content}', $content, $comment[$suffix]);
}


echo "Start merge...\n";

$resCont = comment($distFullname, $suffix)."\r\n\r\n";

$total = $success = $fail = 0;
$errFile = array();

$dirObj = new FilesystemIterator($dirPath, FilesystemIterator::SKIP_DOTS);

foreach ($dirObj as $file) {
	if ($file->isFile() && $file->getExtension() == $suffix) {
		$total++;
		$resCont .= comment($file->getBasename(), $suffix)."\r\n";
		$content = file_get_contents($file->getPathname());
		if ($content !== false) {
			$resCont .= $content."\r\n\r\n";
			$success++;
		} else {
			$errFile[] = $file->getFilename();
		}
	}
}

if ($total == 0) {
	echo 'no specified suffix ('.$suffix.') file';
	exit;
}

if (file_put_contents($distFullname, $resCont)) {
	echo 'Finish...';
	if ($total == $success) {
		echo 'all merge !!';
	} else {
		echo $success.' merge.'."\n";
		if ($errFile) {
			echo 'Fail file name: ';
			foreach ($errFile as $f) {
				echo $f."    ";
			}
		}
	}
}