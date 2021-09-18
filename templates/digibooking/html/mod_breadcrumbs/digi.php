<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_breadcrumbs
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<div aria-label="<?php echo htmlspecialchars($module->title, ENT_QUOTES, 'UTF-8'); ?>" role="navigation">
	<ul itemscope itemtype="https://schema.org/BreadcrumbList" class="uk-flex uk-flex-middle uk-flex-center uk-margin-remove-bottom uk-margin-small-top uk-padding-remove uk-text-zero breadcrumb <?php echo $moduleclass_sfx; ?>">

		<?php
		// Get rid of duplicated entries on trail including home page when using multilanguage
		for ($i = 0; $i < $count; $i++)
		{
			if ($i === 1 && !empty($list[$i]->link) && !empty($list[$i - 1]->link) && $list[$i]->link === $list[$i - 1]->link)
			{
				unset($list[$i]);
			}
		}

		// Find last and penultimate items in breadcrumbs list
		end($list);
		$last_item_key   = key($list);
		prev($list);
		$penult_item_key = key($list);

		// Make a link if not the last item in the breadcrumbs
		$show_last = $params->get('showLast', 1);

		// Generate the trail
		foreach ($list as $key => $item) :
			if ($key !== $last_item_key) :
				// Render all but last item - along with separator ?>
				<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
					<?php if (!empty($item->link)) : ?>
                        <a itemprop="item" href="<?php echo $item->link; ?>" class="pathway">
                            <span itemprop="name"><?php echo $item->name; ?></span>
                        </a>
                    <?php else : ?>
                        <span itemprop="name"><?php echo $item->name; ?></span>
                    <?php endif; ?>
					<?php if (($key !== $penult_item_key) || $show_last) : ?>
                        <span class="divider">&ensp;<img src="<?php echo JUri::base().'images/sprite.svg#chevron-left'; ?>" width="16" height="16" data-uk-svg />&emsp;</span>
                    <?php endif; ?>
					<meta itemprop="position" content="<?php echo $key + 1; ?>">
				</li>
			<?php elseif ($show_last) :
				// Render last item if reqd. ?>
				<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="active">
					<span itemprop="name"><?php echo $item->name; ?></span>
					<meta itemprop="position" content="<?php echo $key + 1; ?>">
				</li>
			<?php endif;
		endforeach; ?>
	</ul>
</div>