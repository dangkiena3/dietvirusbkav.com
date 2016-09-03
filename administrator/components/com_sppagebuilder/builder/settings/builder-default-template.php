<?php
/**
 * @package SP Page Builder
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2015 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/
//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

// variable declation for predefiend column set

$col_grid = array(
	'12' 			=> '1/1',
	'6,6' 		=> '1/2 + 1/2',
	'4,4,4' 	=> '1/3 + 1/3 + 1/3',
	'3,3,3,3' => '1/4 + 1/4 + 1/4 + 1/4',
	'4,8' 		=> '1/3 + 3/4',
	'3,9' 		=> '1/4 + 3/4',
	'3,6,3' 	=> '1/4 + 1/2 + 1/4',
	'2,6,4' 	=> '1/6 + 1/2 + 1/3',
	'2,10' 		=> '1/6 + 5/6',
	'5,7' 		=> '5/12 + 7/12'
);

?>

<div class="clearfix">
	<div class="page-builder-area">
		<?php
			$row_std_sets = array();
			foreach( $sp_builder_row_ops['attr'] as $key => $value ){
				if (!isset($value['std'])) {
					$value['std'] = '';
				}
				$row_std_sets[$key] = $value['std'];
			}

			$row_std_settings = SpPgaeBuilder::getAddonRowColumnConfig( $row_std_sets );
		?>
		<div class="pagebuilder-section" <?php echo $row_std_settings; ?>>

			<div class="section-header clearfix">

				<div class="pull-left">
					<a class="move-row" href="javascript:void(0)"><i class="fa fa-arrows"></i></a>
					<div class="row-layout-container">
						<a class="add-column" href="javascript:void(0)"><i class="fa fa-plus"></i> <?php echo JText::_('COM_SPPAGEBUILDER_ADD_COLUMN'); ?></a>
						<ul>
							<?php foreach ($col_grid as $key => $grid) { ?>
								<li><a href="#" class="row-layout row-layout-<?php echo str_replace(',','',$key); ?> sp-tooltip <?php echo ($key == '12')? 'active':''; ?>" data-layout="<?php echo $key; ?>" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $grid; ?>"></a></li>
							<?php } ?>
							<li><a href="#" class="row-layout row-layout-custom sp-tooltip" data-layout="" data-type="custom" data-toggle="tooltip" data-placement="top" data-original-title="Custom"></a></li>
						</ul>
					</div>
				</div>

				<div class="row-actions pull-right">
					<a class="add-rowplus" href="javascript:void(0)"><i class="fa fa-plus"></i></a>
					<a class="row-options" href="javascript:void(0)"><i class="fa fa-cog"></i></a>
					<a class="duplicate-row" href="javascript:void(0)"><i class="fa fa-files-o"></i></a>
					<a class="delete-row" href="javascript:void(0)"><i class="fa fa-times"></i></a>
				</div>

			</div>

			<?php
			$col_std_sets = array();
			foreach( $sp_builder_col_ops['attr'] as $key => $value ) {
				if (!isset($value['std'])) {
					$value['std'] = '';
				}
				$col_std_sets[$key] = $value['std'];
			}

			$col_std_settings = SpPgaeBuilder::getAddonRowColumnConfig( $col_std_sets );
			?>

			<div class="row">
				<div class="column-parent col-sm-12" <?php echo $col_std_settings; ?>>
					<div class="column">

					</div>

					<div class="col-settings">
						<a class="add-addon" href="javascript:void(0)"><i class="fa fa-plus-circle"></i> Addon</a>
						<a class="column-options" href="javascript:void(0)"><i class="fa fa-cog"></i> Options</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>