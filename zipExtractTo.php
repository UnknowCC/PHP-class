<?php 

function zipExtractTo($destination, $source)
{
	if (!is_writable($destination)) {
		throw new Exception($destination.'指定目录权限不足');
	}

	if (!class_exists('ZipArchive')) {
		die('Zip扩展未开启');
	}

	$za = new ZipArchive();
	$res = $za->open($source);
	if ($res !== TRUE) {
		throw new Exception($source.'文件打开失败，错误编号:'.$res);
	}
	$sqlContent = '';
	for ($i=0; $i < $za->numFiles; $i++) { 
		$stat = $za->statIndex($i);
		// echo $filename."\n";
		if ($stat['size'] > 0 && !preg_match('/\.sql$/', $stat['name'])) {
			$za->extractTo(ROOT_PATH.'test', $stat['name']);
		}
		if ($stat['size'] == 0) { // 目录
			continue;
		}
		if (preg_match('/\.sql$/', $stat['name'])) {
			if (($content = $za->getFromIndex($stat['index'])) !== FALSE) {
				$content = preg_replace_callback('/^\s*(--|\#)(.*?)$/m', function ($matches) {
					return '';
				}, $content);
				if (($content = trim($content)) != '') {
					$sqlContent .= $content;
				}
				
			} else {
				throw new Exception('无法获取'.$stat['name'].'的文件内容');
			}
			continue;
		}
		$extRst = $za->extractTo($destination, $stat['name']);
		if ($extRst === FALSE) {
			throw new Exception($stat['name'].'文件解压失败');
		}
	}
	$za->close();

	if ($sqlContent != '') {
		// sql 语句写入数据库
		$sqls = implode(';', $sqlContent);
		try {
			DB::startTransaction();
			foreach ($sqls as $sql) {
				$sql = trim($sql);
				$rst = DB::query($sql);
				if (DB::errno()) {
					throw new Exception(DB::error());
				}
			}
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
		}
		
	}
}


class DB
{
	

}
