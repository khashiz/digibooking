<?php
/** 
 * @package   	VikAppointments
 * @subpackage 	com_vikappointments
 * @author    	Matteo Galletti - e4j
 * @copyright 	Copyright (C) 2019 e4j - Extensionsforjoomla.com. All Rights Reserved.
 * @license  	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link 		https://extensionsforjoomla.com
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$reviews 		= isset($displayData['reviews'])			? $displayData['reviews'] 			: array();
$can_leave 		= isset($displayData['canLeave'])			? $displayData['canLeave'] 			: false;
$ordering_links	= isset($displayData['orderingLinks'])		? $displayData['orderingLinks'] 	: false;
$id_service 	= isset($displayData['id_service'])			? $displayData['id_service'] 		: 0;
$id_employee 	= isset($displayData['id_employee'])		? $displayData['id_employee'] 		: 0;
$rev_sub_title 	= isset($displayData['subtitle']) 			? $displayData['subtitle'] 			: '';
$dt_format 		= isset($displayData['datetime_format']) 	? $displayData['datetime_format'] 	: '';
$itemid 		= isset($displayData['itemid']) 			? $displayData['itemid'] 			: null;

if ($id_service)
{
	// reviews for a service
	$col_name 	= 'id_ser';
	$col_value 	= $id_service;
	$leave_task = 'submit_service_review';
}
else
{
	// reviews for an employee
	$col_name 	= 'id_emp';
	$col_value 	= $id_employee;
	$leave_task = 'submit_employee_review';
}

if (is_null($itemid))
{
	// item id not provided, get the current one (if set)
	$itemid = JFactory::getApplication()->input->getInt('Itemid');
}

$reviews_load_mode = VikAppointments::getReviewsLoadMode();

$MIN_COMMENT_LENGTH 		= VikAppointments::getReviewsCommentMinLength();
$MAX_COMMENT_LENGTH 		= VikAppointments::getReviewsCommentMaxLength();
$REVIEW_COMMENT_REQUIRED 	= VikAppointments::isReviewsCommentRequired();

?>

<div class="vap-allreviews-intro">

	<div class="vap-allreviews-title">
		<h2><?php echo JText::_('VAPREVIEWSTITLE'); ?></h2>
		<span><?php echo $rev_sub_title; ?></span>
	</div>

	<div class="vap-allreviews-actions">
		<?php foreach ($ordering_links as $link) { ?>
			<a href="<?php echo JRoute::_($link['uri']); ?>" class="vap-revord-link <?php echo ($link['active'] ? 'active' : ''); ?>">
				<i class="fa fa-<?php echo $link['mode'] == 'ASC' ? 'sort-amount-asc' : 'sort-amount-desc'; ?>"></i>
				<span><?php echo $link['name']; ?></span>
			</a>
		<?php } ?>
	</div>
</div>

<?php if ($can_leave) { ?>
	
	<div class="vap-postreview-block">
		
		<div class="vap-postreview-form" style="display: none;">
			<form action="<?php echo JRoute::_('index.php?option=com_vikappointments&task=' . $leave_task . ($itemid ? '&Itemid=' . $itemid : '')); ?>" method="post" id="vaprevformpost">
				
				<div class="vap-postreview-top">
					
					<!-- Rating -->
					<div class="vap-postreview-ratingwrap">
						<div class="vap-postreview-label"><?php echo JText::_('VAPPOSTREVIEWLBLRATING'); ?>*</div>
						<div class="vap-postreview-field">
							<div class="vap-rating-field">
								<?php for ($i = 1; $i <= 5; $i++) { ?>
									<div class="vap-rating-box rating-nostar" id="vaprating<?php echo $i; ?>"></div>
								<?php } ?>
							</div>
							<input type="hidden" name="rating" value="" id="vapreviewrating"/>
						</div>
					</div>
					
					<!-- Title -->
					<div class="vap-postreview-titlewrap">
						<div class="vap-postreview-label"><?php echo JText::_('VAPPOSTREVIEWLBLTITLE'); ?>*</div>
						<div class="vap-postreview-field">
							<input type="text" name="title" value="" maxlength="64" size="32" id="vapreviewtitle"/>
						</div>
					</div>

				</div>
				
				<!-- Comment -->
				<div class="vap-postreview-label">
					<?php echo JText::_('VAPPOSTREVIEWLBLCOMMENT'); ?>
					<?php echo ($REVIEW_COMMENT_REQUIRED ? '*' : ''); ?>
				</div>
				
				<div class="vap-postreview-field">
					<div class="vap-postreview-commentarea">
						
						<textarea maxlength="<?php echo $MAX_COMMENT_LENGTH; ?>" name="comment" id="vapreviewcomment"></textarea>
						
						<div class="vap-postreview-charsleft">
							<span><?php echo JText::_('VAPPOSTREVIEWCHARSLEFT'); ?>&nbsp;</span>
							<span id="vapcommentchars"><?php echo $MAX_COMMENT_LENGTH; ?></span>
						</div>

						<?php if ($MIN_COMMENT_LENGTH > 0) { ?>
							<div class="vap-postreview-minchars">
								<span><?php echo JText::_('VAPPOSTREVIEWMINCHARS'); ?>&nbsp;</span>
								<span id="vapcommentminchars"><?php echo $MIN_COMMENT_LENGTH; ?></span>
							</div>
						<?php } ?>
					</div>
				</div>
			
				<input type="hidden" name="option" value="com_vikappointments" />
				<input type="hidden" name="task" value="<?php echo $leave_task; ?>" />
				<input type="hidden" name="<?php echo $col_name; ?>" value="<?php echo $col_value; ?>" />
			</form>
		</div>
		
		<div class="vap-postreview-bottom">
			<button type="button" class="vap-btn blue" onClick="vapLeaveReview(this);"><?php echo JText::_('VAPLEAVEREVIEWLINK'); ?></button>
		</div>
		
	</div>
	
<?php } ?>

<div class="vap-reviews-cont">
	<div class="vap-reviews-list">
		
		<?php
		foreach ($reviews['rows'] as $review)
		{
			$data = array(
				/**
				 * @param 	array 	 review 	 	  An associative array containing the review details.
				 */
				'review' => $review,

				/**
				 * @param 	string   datetime_format  The date time format used to display when the review was created.
				 * 									  If not provided, it will be used the default one (military format).
				 */
				'datetime_format' => $dt_format,
			);

			/**
			 * The review block is displayed from the layout below:
			 * /components/com_vikappointments/layouts/review/default.php
			 * 
			 * If you need to change something from this layout, just create
			 * an override of this layout by following the instructions below:
			 * - open the back-end of your Joomla
			 * - visit the Extensions > Templates > Templates page
			 * - edit the active template
			 * - access the "Create Overrides" tab
			 * - select Layouts > com_vikappointments > review
			 * - start editing the default.php file on your template to create your own layout
			 *
			 * @since 1.6
			 */
			echo JLayoutHelper::render('review.default', $data);
		}
		?>

	</div>
</div>

<?php
if ($reviews_load_mode == 2 && $reviews['size'] > count($reviews['rows']))
{
	// AJAX disabled, load reviews with the apposite buttons
	?>
	<div class="vap-reviews-load-wrap">
		<button class="vap-btn blue" onClick="loadMoreReviews();">
			<?php echo JText::_('VAPREVIEWLOADMOREBTN'); ?>
		</button>
	</div>
	<?php
}
?>

<div id="vap-reviews-limit"></div>

<script>

	var LOAD_REVIEWS 		= true;
	var REVIEWS_START_LIM 	= <?php echo count($reviews['rows']); ?>;
	var ALL_LOADED 			= <?php echo ($reviews['size'] > count($reviews['rows']) ? 0 : 1); ?>;

	jQuery(document).ready(function() {

		if (document.location.search.indexOf('revordby') != -1) {
			vapDoAnimation = false;
			jQuery('html,body').animate( {scrollTop: (jQuery('.vap-allreviews-title').first().offset().top-5)}, {duration:'normal'} );
			day = '';
		}

		<?php if ($reviews_load_mode == 1) { ?>

			// load the reviews via AJAX when the scrollbar touches the limit
			jQuery(window).scrollStopped(function() {
				if (LOAD_REVIEWS && isReviewsLimitReached()) {
					loadMoreReviews();  
				}
			});

		<?php } ?>

	});
	
	function showMoreLessDescription(action, id) {
		var app = jQuery('#vaprevcomment' + id).text();
		jQuery('#vaprevcomment' + id).text(jQuery('#vaprevcomfull'+id).val());
		jQuery('#vaprevcomfull' + id).val(app);
		
		if (jQuery('#vaprevcomtype'+id).val() == 'more') {
			jQuery(action).text('<?php echo addslashes(JText::_('VAPREVIEWCOMMENTSHOWLESS')); ?>');
			jQuery('#vaprevcomtype'+id).val('less');
		} else {
			jQuery(action).text('<?php echo addslashes(JText::_('VAPREVIEWCOMMENTSHOWMORE')); ?>');
			jQuery('#vaprevcomtype'+id).val('more');
		}
	}
	
	function loadMoreReviews(attempt) {
		if (ALL_LOADED) {
			return;
		}

		if (!attempt) {
			attempt = 1;
		}
		
		LOAD_REVIEWS = false;
		
		jQuery.noConflict();
				
		var jqxhr = jQuery.ajax({
			type: "POST",
			url: "<?php echo JRoute::_('index.php?option=com_vikappointments&task=load_more_reviews&tmpl=component' . ($itemid ? '&Itemid=' . $itemid : ''), false); ?>",
			data: {
				<?php echo $col_name; ?>: <?php echo $col_value; ?>,
				lim0: REVIEWS_START_LIM
			}
		}).done(function(resp) {
			var obj = jQuery.parseJSON(resp); 
			
			if (obj[0]) {

				jQuery.each(obj[3], function(k, v) {
					jQuery('.vap-reviews-list').append(v);
				});
				
				REVIEWS_START_LIM += obj[1];
				ALL_LOADED = (REVIEWS_START_LIM >= obj[2]);
				
				if (ALL_LOADED) {
					jQuery('.vap-reviews-load-wrap').hide();
				}
				
				LOAD_REVIEWS = true;
			} else {
				LOAD_REVIEWS = true;
			}
		}).fail(function(resp) {

			console.log(resp, resp.statusText);

			/**
			 * Repeat loading in case of failure.
			 * The attempt will be repeated at most for 3 times.
			 * 
			 * @since 1.6
			 */
			 if (attempt < 3) {
			 	// wait some milliseconds before to redo AJAX
			 	setTimeout(function() {
			 		loadMoreReviews(attempt + 1);
			 	}, 128);
			 }

		});
	}
	
	function isReviewsLimitReached() {
		var rev_limit_y = jQuery('#vap-reviews-limit').offset().top;
		var scroll = jQuery(window).scrollTop();
		var screen_height = jQuery(window).height();
		
		if (rev_limit_y - scroll - 150 > screen_height) {
			return false;
		}
		
		return true;
	}
	
	jQuery.fn.scrollStopped = function(callback) {
		var elem = jQuery(this), self = this;
		elem.scroll(function() {
			if (elem.data('scrollTimeout')) {
			  clearTimeout(elem.data('scrollTimeout'));
			}
			elem.data('scrollTimeout', setTimeout(callback, 250, self));
		});
	};
	
	// SUBMIT REVIEW
	
	function vapLeaveReview(action) {
		var form = jQuery('.vap-postreview-form');
		if (!form.is(':visible')) {
			form.slideDown();
			jQuery(action).text('<?php echo addslashes(JText::_('VAPSUBMITREVIEWLINK')); ?>');
			return;
		}
		
		if (vapValidatesReviewFields()) {
			jQuery('#vaprevformpost').submit();
		}
		
	}
	
	function vapValidatesReviewFields() {
		var input = ['title', 'rating'];
		
		var resp = true;
		jQuery.each(input, function(i,v){
			var elem = jQuery('#vapreview'+v);
			if (elem.val().length > 0) {
				elem.parent().prev().removeClass('vap-reviewfield-required');
			} else {
				elem.parent().prev().addClass('vap-reviewfield-required');
				resp = false;
			}
		});
		
		if (resp && REVIEW_COMMENT_REQUIRED) {
			var elem = jQuery('#vapreviewcomment');
			if (elem.val().length > 0) {
				elem.parent().parent().prev().removeClass('vap-reviewfield-required');
			} else {
				elem.parent().parent().prev().addClass('vap-reviewfield-required');
				resp = false;
			}
		}
		
		if (resp) {
			var comment = jQuery('#vapreviewcomment');
			if (comment.val().length > 0 && comment.val().length < MIN_COMMENT_LENGTH) {
				comment.parent().parent().prev().addClass('vap-reviewfield-required');
				jQuery('.vap-postreview-minchars').addClass('vap-reviewfield-required');
				resp = false;
			} else {
				comment.parent().prev().removeClass('vap-reviewfield-required');
				jQuery('.vap-postreview-minchars').removeClass('vap-reviewfield-required');
			}
		}
		
		return resp;
	}

	var TO_RATE = true;
	var MAX_COMMENT_LENGTH = <?php echo $MAX_COMMENT_LENGTH; ?>;
	var MIN_COMMENT_LENGTH = <?php echo $MIN_COMMENT_LENGTH; ?>;
	var REVIEW_COMMENT_REQUIRED = <?php echo $REVIEW_COMMENT_REQUIRED ? 1 : 0; ?>;
	
	jQuery(document).ready(function() {

		jQuery('.vap-rating-box').on('click', function() {
			var id = jQuery(this).attr('id').split('vaprating')[1];
			
			jQuery('.vap-rating-box').removeClass('rating-nostar rating-hoverstar rating-yesstar');
			
			if (TO_RATE) {
				jQuery(this).addClass('rating-yesstar');
				jQuery(this).siblings().each(function() {
					if (jQuery(this).attr('id').split('vaprating')[1] < id) {
						jQuery(this).addClass('rating-yesstar');
					} else {
						jQuery(this).addClass('rating-nostar');
					}
				});
				
				jQuery('#vapreviewrating').val(id);
			} else {
				jQuery(this).addClass('rating-hoverstar');
				jQuery(this).siblings().each(function(){
					if (jQuery(this).attr('id').split('vaprating')[1] < id) {
						jQuery(this).addClass('rating-hoverstar');
					} else {
						jQuery(this).addClass('rating-nostar');
					}
				});
				
				jQuery('#vapreviewrating').val('');
			}
			
			TO_RATE = !TO_RATE
		});
		
		jQuery('.vap-rating-box').hover(function() {
			var id = jQuery(this).attr('id').split('vaprating')[1];
			
			if (TO_RATE) {
				jQuery('.vap-rating-box').removeClass('rating-nostar rating-hoverstar rating-yesstar');
				
				jQuery(this).addClass('rating-hoverstar');
				jQuery(this).siblings().each(function(){
					if (jQuery(this).attr('id').split('vaprating')[1] < id) {
						jQuery(this).addClass('rating-hoverstar');
					} else {
						jQuery(this).addClass('rating-nostar');
					}
				});
			}
			
		}, function() {
			
		});
		
		jQuery('#vapreviewcomment').on('keyup', function(e) {
			jQuery('#vapcommentchars').text((MAX_COMMENT_LENGTH - jQuery(this).val().length));       
		});

	});

</script>
