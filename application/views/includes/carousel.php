<div id="big-carousel" class="carousel slide" data-ride="carousel">
	<!-- Indicators -->
	<ol class="carousel-indicators">
		<?php
		$count = 0;
		foreach($carousel_array as $slide)
		{
			if(!$slide->disabled)
			{
				if($count === 0)
				{
					echo '<li data-target="#big-carousel" data-slide-to="0" class="active"></li>';
				}
				else
				{
					echo'<li data-target="#big-carousel" data-slide-to="'.$count.'"></li>';
				}
				$count++;
			}
		}
		?>
	</ol>

	<!-- Wrapper for slides -->
	<div class="carousel-inner">

		<?php
		$count = 0;
		foreach($carousel_array as $slide)
		{
			$active = '';
			if($count === 0)
			{
				$active = 'active';
			}
			if($slide->carousel_type == 1 && !$slide->disabled)
			{
				echo '
				<div class="item darkbg '.$active.'">
					<div class="container photo">
						<div class="row">
							<div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
								<h1 class="text-center">
									'.$slide->title.'
								</h1>
							</div>
						</div>
						<div class="row">
							<div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
								<iframe src="'.$slide->content.'"
									width="100%" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
							</div>
						</div>
					</div>
				</div>
				';
				$count++;
			}
			elseif($slide->carousel_type == 2 && !$slide->disabled)
			{
				$photos = array();
				foreach($slide->photos as $photo)
				{
					array_push($photos, array('file' => $photo->image_original_filename, 'photo' => $photo->photo, 'link' => $photo->link, 'filter_blurred' => $photo->filter_blurred, 'filter_grayscale' => $photo->filter_grayscale));
				}

				$img = rand(0, count($photos)-1);


				$photo_image = '';
				$photo_photo = '';
				$photo_link = '';

				$photo_blur = '';


				if(count($photos) > 0)
				{
					if($photos[$img]['filter_blurred'] == 1 && $photos[$img]['filter_grayscale'] == 1)
						$photo_blur = 'grayscale-blur';
					elseif($photos[$img]['filter_grayscale'] == 1)
						$photo_blur = 'grayscale';
					elseif($photos[$img]['filter_blurred'] == 1)
						
					$photo_blur = 'blur';
					$photo_image = '<div class="image '.$photo_blur.'" style="background-image: url('.base_url().'user_content/images/original/'.$photos[$img]['file'].');"></div>';
					$photo_photo = $photos[$img]['link'];
					$photo_link = $lang['misc_photo'].': '.$photos[$img]['photo'];
				}

				echo '
				<div class="item photo '.$active.'">
					'.$photo_image.'
					<div class="carousel-caption">
						<h1>
							'.$slide->title.'
						</h1>
						<p class="lead">
							'.$slide->content.'<br />
							<a href="'.$photo_photo.'" target="_blank">'.$photo_link.'</a>
						</p>
					</div>
				</div>
				';

				$count++;
			}
		}
		?>
	</div>

	<!-- Controls -->
	<a class="left carousel-control hidden-sm hidden-xs" href="#big-carousel" data-slide="prev">
		<span class="glyphicon glyphicon-chevron-left"></span>
	</a>
	<a class="right carousel-control hidden-sm hidden-xs" href="#big-carousel" data-slide="next">
		<span class="glyphicon glyphicon-chevron-right"></span>
	</a>
</div>
