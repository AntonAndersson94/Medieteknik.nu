<?php
if($message == 'success')
	echo '<div class="alert alert-success">'.$lang['misc_done'].'</div>';
if($message == 'fail')
	echo '<div class="alert alert-danger">'.$lang['error_error'].'</div>';
?>
<div class="main-box clearfix box-body">
	<h2><?php echo $lang['admin_forum']; ?></h2>
</div>
<?php
if(count($reports) > 0)
{
	?>
	<div class="main-box clearfix margin-top box-body">
		<h3>
			<?php echo $lang['admin_forum_reported']; ?>
		</h3>
		<?php
		foreach ($reports as $report) {
			?>
			<h5>
				<?php
				echo readable_date($report->report_date, $lang).', '.$report->lukasid.' '.
					$lang['admin_forum_reported_by'].' '.$report->p_lukasid.'s '.$lang['misc_post'].' '.
					anchor('forum/thread/'.$report->topic_id.'#replyid-'.$report->reply_id, $lang['admin_forum_show_post']);
				?>
			</h5>
			<div class="row">
				<div class="col-sm-9">
					<p>
						<?php echo news_excerpt(text_format($report->reply, '<p>','</p>', TRUE), 200); ?>...
					</p>
				</div>
				<div class="col-sm-3">
					<p>
						<?php
						echo anchor('admin/forum/remove_report/'.$report->report_id, '<span class="glyphicon glyphicon-trash"></span> '.$lang['admin_forum_remove'], array('class' => 'btn btn-danger btn-sm btn-block'));
						?>
					</p>
				</div>
			</div>
			<?php
		}
		?>
	</div>
	<?php
}
?>
<?php
if(count($pending) > 0)
{
	?>
	<div class="main-box clearfix margin-top box-body">
		<h3>
			<?php echo $lang['admin_forum_pending']; ?>
		</h3>
		<?php
		foreach ($pending as $post)
		{
			?>
			<div class="row">
				<div class="col-sm-4">
					<h5>
						<?php echo anchor('forum/thread/'.$post->topic_id, $post->topic); ?>
					</h5>
					<p>
						<?php
						echo '<strong>'.$post->name.'</strong> <em>'.$post->email.'</em> '.readable_date($post->reply_date, $lang);
						?>
					</p>
					<p>
						<?php
						echo anchor('admin/forum/verify/'.$post->reply_id, $lang['admin_forum_approve'], array('class' => 'btn btn-success btn-sm btn-block'));
						?>
					</p>
				</div>
				<div class="col-sm-8">
					<?php echo text_format($post->reply, '<p>','</p>', TRUE); ?>
				</div>
			</div>
			<?php
		}
		?>
	</div>
	<?php
}
?>
