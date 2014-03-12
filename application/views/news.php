<?php
foreach($news_array as $news_item)
{
	// do_dump($news_item);
	$img_div = "";
	$news_class = "main-box";
	$style = "";
	if($news_item->image_original_filename != "")
	{
		$image = new imagemanip();
		$image->create($news_item->image_original_filename, 'zoom', news_size_to_px($news_item->size), $news_item->height);

		$img_div = '<img class="'.news_size_to_class($news_item->size).'" src="'.$image->get_filepath().'" alt="'.$news_item->title.'" />';
		$news_class = news_size_to_class_invert($news_item->size);
		$style = 'max-height:'.$news_item->height.'px; overflow: hidden;';
	}

	$lang_img = '<img src="'.lang_id_to_imgpath($news_item->lang_id).'" alt="flag" class="news_flag" />';
	$news_div = '<div style="'.$style.'" class="'.$news_class.'">'.$lang_img.'<h2>'.$news_item->title.'</h2><p>'.text_format($news_item->text, '<p>','</p>', FALSE).'</p></div>';

	$story = "";
	if($news_item->position == 1 || $news_item->size == 4)
	{
		$story = $img_div.$news_div;
	} else {
		$story = $news_div.$img_div;
	}
	// echo anchor('news/view/'.$news_item->id, $story, array("class" => "main-box news clearfix", "title" => $lang['news_tothenews'] ));

	$news_story = text_format($news_item->text, '<p>','</p>', FALSE);
	?>
	<div class="main-box news clearfix">
		<h2>
			<?php echo anchor('news/view/'.$news_item->id, $news_item->title, array("title" => $lang['news_tothenews'])); ?>
			<?php
			if($news_item->draft)
				echo '<span class="label label-default">'.$lang['misc_draft'].'</span>';
			?>
			<img src="<?php echo lang_id_to_imgpath($news_item->lang_id); ?>" class="img-circle pull-right" />
		</h2>
		<h3>
			<?php echo $lang['misc_published']; ?>
			<i class="date" title="<?php echo $news_item->date; ?>">
				<?php echo readable_date($news_item->date, $lang); ?>
			</i>
			<?php echo $lang['misc_by'].' '.anchor('user/profile/'.$news_item->userid, $news_item->first_name.' '.$news_item->last_name); ?>
		</h3>
		<?php echo $news_story; ?>
	</div>
	<?php
}

$total_pages = floor($news_count / $news_limit)+1;
$prev_page = $news_page == 1 ? 1 : $news_page-1;
$next_page = $news_page == $total_pages ? $total_pages : $news_page+1;

$threshold = 3;
?>
<ul class="pagination center-block">
	<li<?php echo $news_page == 1 ? ' class="disabled"' : '';?>>
		<?php echo anchor('news/archive/page/'.$prev_page.'/'.$news_limit, '&laquo;'); ?>
	</li>

	<?php
		$start = $news_page-$threshold > 0 ? $news_page-$threshold : 1;
		$end = $news_page+$threshold <= $total_pages ? $news_page+$threshold : $total_pages;

		if($news_page > $threshold+1)
		{
			echo '<li>'.anchor('news/archive/page/1/'.$news_limit, 1).'</li>';
			echo '<li class="disabled">'.anchor('#', '...', 'onClick="return false;"');'</li>';
		}

		for($k = $start; $k <= $end; $k++)
		{
			?>
			<li<?php echo $k == $news_page ? ' class="active"' : '';?>>
				<?php echo anchor('news/archive/page/'.$k.'/'.$news_limit, $k); ?>
			</li>
			<?php
		}

		if($news_page < $total_pages-$threshold)
		{
			echo '<li class="disabled">'.anchor('#', '...', 'onClick="return false;"');'</li>';
			echo '<li>'.anchor('news/archive/page/'.$total_pages.'/'.$news_limit, $total_pages).'</li>';
		}
	?>

	<li<?php echo $news_page == $total_pages ? ' class="disabled"' : '';?>>
		<?php echo anchor('news/archive/page/'.$next_page.'/'.$news_limit, '&raquo;'); ?>
	</li>
</ul>
