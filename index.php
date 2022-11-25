<?php

function convertPNGsToJPGs($file) {
	if($file->extension() != 'png') return;

	$excludeTemplates = option('mrflix.pngs-to-jpgs.excludeTemplates');
	$excludePages = option('mrflix.pngs-to-jpgs.excludePages');
	$excludedByTemplate = false;
	$excludedByPage = false;
	if($file->page()) {
		if(!empty($excludeTemplates)) $excludedByTemplate = in_array($file->page()->intendedTemplate(), $excludeTemplates);
		if(!empty($excludePages)) $excludedByPage = in_array($file->page()->uid(), $excludePages);
	}
	if($excludedByTemplate || $excludedByPage) return;

	$path = $file->contentFileDirectory() . '/';
	$input = $path . $file->filename();
	$output = $path . $file->name() . '.jpg';
	
	try {
		if(option('thumbs.driver') == 'gd'){
			// https://stackoverflow.com/a/8951540/731172
			$image = imagecreatefrompng($input);
			$bg = imagecreatetruecolor(imagesx($image), imagesy($image));
			$bgcolor = option('mrflix.pngs-to-jpgs.background') == 'black' ? imagecolorallocate($bg, 0, 0, 0) : imagecolorallocate($bg, 255, 255, 255);
			imagefill($bg, 0, 0, $bgcolor);
			imagealphablending($bg, TRUE);
			imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
			imagedestroy($image);
			imagejpeg($bg, $output, option('mrflix.pngs-to-jpgs.quality'));
			imagedestroy($bg);
		} else if(option('thumbs.driver') == 'im'){
			$command = [option('thumbs.bin', 'convert')];
			$command[] = $input;
			$command[] = '-background '. option('mrflix.pngs-to-jpgs.background');
			$command[] = '-flatten';
			$command[] = '-quality '. option('mrflix.pngs-to-jpgs.quality');
			$command[] = $output;
			$command = implode(' ', $command);
			file_put_contents('php://stderr', $command.PHP_EOL);

			exec($command, $out, $return);

			if ($return !== 0) {
				throw new Exception('The imagemagick convert command could not be executed: ' . $command);
			}
		}

		$file->delete();
	}
	catch (Exception $e) {
		throw new Exception($e->getMessage());
	}
}

Kirby::plugin('mrflix/pngs-to-jpgs', [
	'options' => [
		'background' => 'white',
		'quality' => 90,
		'excludeTemplates' => [],
		'excludePages' => []
	],
	'hooks' => [
		'file.create:after' => function ($file) {
			convertPNGsToJPGs($file);
		},
		'file.replace:after' => function ($newFile, $oldFile) {
			convertPNGsToJPGs($newFile);
		}
	]
]);