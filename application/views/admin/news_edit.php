<?php

// prepare all the data
$post_date = array(		'name'        => 'post_date',
						'id'          => 'post_date',
						'placeholder' => $lang['date_placeholder'],
						'class'		  => 'form-control',
						'type' 		  => 'datetime-local'
					);

$img_height = array(	'name'        => 'img_height',
						'id'          => 'img_height',
						'value'       => '',
					);
$img_file = array(		'name'        => 'img_file',
						'id'          => 'img_file',
					);
$options = 	array(		'1'  => '1/3',
						'2'    => '1/2',
						'3'   => '2/3',
						'4'   => '1/1',
					);
$pos = array(			'1'  => $lang['misc_left'],
						'2'    => $lang['misc_right'],
					);
$draft = array(			'name'        => 'draft',
						'id'          => 'draft',
						'value'       => '1',
						'checked'     => TRUE,
					);
$approved = array(		'name'        => 'approved',
						'id'          => 'approved',
						'value'       => '1',
						'checked'     => FALSE,
					);
$news_approved = 0;
$news_size = 0;
$news_position = 0;
$news_height = 100;

// hack so that the same view can be used for both create and edit
$image_div = "";
$action = 'admin_news/edit_news/0';
if(isset($news) && $news != false) {
	$post_date['value'] = date('Y-m-d\TH:i:s', strtotime($news->date)); // fix for datetime-local date format
	$draft['checked'] = ($news->draft == 1);
	$approved['checked'] = ($news->approved == 1);
	$news_approved = $news->approved;
	$news_size = $news->size;
	$news_position = $news->position;
	$news_height = ($news->height == '') ? $news_height : $news->height;
	$action = 'admin_news/edit_news/'.$id;

	if($news->image_original_filename != "") {
		$image = new imagemanip($news->image_original_filename, 'zoom', news_size_to_px($news->size), $news->height);
		$image_div = '<div><img src="'.$image.'"/></div>';
	}
}



// do all the printing
echo
form_open_multipart($action),
'<div class="main-box clearfix">
	<h2>', $lang['admin_editnews'], '</h2>',
	'<div class="row">',
		'<div class="col-sm-4">',
			'<label class="checkbox-inline">
				',form_checkbox($draft),
				' ',$lang['misc_draft'],
			'</label>';

			if($is_editor)
			{
				echo '<label class="checkbox-inline">',
					form_checkbox($approved),
					' ', $lang['misc_approved'],
				'</label>';
			}
			else
			{
				echo form_hidden($lang['misc_approved'], array('name' => 'approved','id' => 'approved', 'value' => $news_approved));
			}

		echo '<p><input type="submit" name="save" id="save" value="',$lang['misc_save'],'" class="btn btn-success form-control" /></p>',
		'</div>',
		'<div class="col-sm-4">',
			'<p>',
				form_label($lang['misc_postdate'], 'post_date'),
				form_input($post_date),
			'</p>',
		'</div>';
		if(isset($news) && $news != false)
		{
			echo '<div class="col-sm-4">',
				'<p>',
					form_label($lang['admin_news_delete'], 'delete'),
					anchor('admin_news/delete/'.$id,
						'<span class=\'glyphicon glyphicon-trash\'></span> '.$lang['misc_delete'],
						array('class' => 'btn btn-danger form-control')
						),
				'</p>',
			'</div>';
		}
	echo '</div>',
'</div>
<div class="main-box clearfix margin-top" id="image-edit">
	<h2>'.$lang['misc_image'].'</h2>',
	$image_div,
	'<div>',
		form_label($lang['misc_size'], 'img_size'),
		form_dropdown('img_size', $options, $news_size, 'id="img_size"'),
	'</div>
	<div>',
		form_label($lang['misc_position'], 'img_position'),
		form_dropdown('img_position', $pos, $news_position, 'id="img_position"'),
	'</div>
	<div>',
		form_label($lang['misc_height'], 'img_height'),
		'<input type="number" min="75" max="400" name="img_height" id="img_height" value="'.$news_height .'" />',
	'</div>
	<div>',
		form_upload($img_file),
	'</div>
</div>';
//do_dump($image_array);
if (count($images_array) > 0) {
	echo '<div class="main-box clearfix">';
	foreach($images_array as $img) {
		echo
		'<div class="image_overview" style="display: inline-block; width: 110px; height: 150px; overflow:hidden; clear:both;">',
			$img->image->get_img_tag(),
			'<input type="text" value="[img id=',substr($img->image_original_filename, 0, -4),' w=150 h=100]" disabled="disabled" style="width: 100px;" />',
		'</div>';
	}
	echo '</div>';
}


// hack so that the same view can be used for both create and edit
if(isset($news) && $news != false)
	$arr = $news->translations;
else
	$arr = $languages;

echo '<div class="row">';
	foreach($arr as $t) {
		// hack so that the same view can be used for both create and edit
		if(isset($news) && $news != false) {
			$t_title = $t->title;
			$t_text = $t->text;
			$language_abbr = $t->language_abbr;
			$language_name = $t->language_name;
			$lang_id = $t->lang_id;
		}
		else
		{
			$t_title = '';
			$t_text = '';
			$language_abbr = $t['language_abbr'];
			$language_name = $t['language_name'];
			$lang_id = $t['id'];
		}

		$title = array(
	              'name'        => 'title_'.$language_abbr,
	              'id'          => 'title_'.$language_abbr,
	              'value'       => $t_title,
	              'class' 		=> 'form-control'
	            );
		$text = array(
	              'name'        => 'text_'.$language_abbr,
	              'id'          => 'text_'.$language_abbr,
	              'rows'		=>	15,
	              'class' 		=> 'form-control'
	            );

		echo '
		<div class="col-sm-6">
			<div class="main-box clearfix margin-top">
				<h4>',$language_name,' <img src="'.lang_id_to_imgpath($lang_id).'" class="img-circle" /></h4>',
				'<p>',
					form_label($lang['misc_headline'], 'title_'.$language_abbr),
					form_input($title),
				'</p>',
				'<p>',
					form_label($lang['misc_text'], 'text_'.$language_abbr),
					form_textarea($text,$t_text),
				'</p>
			</div>
		</div>';
	}
echo '</div>';
echo form_close();

echo "<script src='".base_url()."/web/js/libs/jquery.min.js'></script>
<script src='".base_url()."/web/js/load_images.js'></script>";
