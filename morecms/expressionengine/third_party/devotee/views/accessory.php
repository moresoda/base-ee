<div class="border">
	<div class="border">

		<?php if(!empty($updates->error)) : ?>
			<p><?php echo $updates->error ?></p>
		<?php else : ?>
			<table>
				<thead>
					<tr class="first">
						<th class="addon-notes">&nbsp;</th>
						<th class="addon-name">Add-On Name</th>
						<th class="addon-type">Type</th>
						<th class="addon-installed">Installed</th>
						<th class="addon-current">Latest</th>
						<th class="addon-status"><span>Status</span></th>
						<th class="addon-link">Link</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($updates as $addon) : ?>
						<?php
							// convert to object if need be on some php versions
							if( ! is_object($addon))
							{
								$addon = (object) $addon;
							}
						?>
						<?php if($addon->update_available) : ?>
							<tr class="update<?php if($addon->notes) : ?> notes<?php endif ?>">
						<?php else : ?>
							<tr>
						<?php endif ?>
							<!-- notes -->
							<?php if($addon->update_available && $addon->notes) : ?>
								<td class="addon-notes"><a href="#" class="toggle"></a></td>
							<?php else : ?>
								<td class="addon-notes">&nbsp;</td>
							<?php endif ?>

							<!-- name -->
							<td class="addon-name"><?php echo $addon->name ?></td>

							<!-- types -->
							<td class="addon-type">
								<ul>
									<?php foreach($addon->types as $key => $val) : ?>
										<li class="<?php echo ($val) ? 'highlight' : '' ?>"><?php echo $key ?></li>
									<?php endforeach ?>
								</ul>
							</td>

							<!-- local version -->
							<td class="addon-installed"><?php echo $addon->version ?></td>

							<!-- devot:ee version -->
							<td class="addon-current">
								<?php if($addon->current_version != '') : ?>
									<?php if($addon->update_available) : ?>
										<span class="available"><?php echo $addon->current_version ?></span>
									<?php else : ?>
										<?php echo $addon->current_version ?>
									<?php endif ?>
								<?php else : ?>
									&ndash;
								<?php endif ?>
							</td>

							<!-- status and link -->
							<?php if($addon->devotee_link != '') : ?>
								<?php if($addon->update_available) : ?>
									<td class="addon-status">
										<span class="available update">Update Available</span>
									</td>
								<?php else : ?>
									<?php if($addon->current_version == '') : ?>
										<td class="addon-status">
											<span class="warning">Version info not available on devot:ee</span>
										</td>
									<?php elseif($addon->version != $addon->current_version) : ?>
										<td class="addon-status">
											<span class="warning">Installed version higher than devot:ee's</span>
										</td>
									<?php else : ?>
										<td class="addon-status">
											<span class="check">Up-to-date</span>
										</td>
									<?php endif ?>
								<?php endif ?>
								<td class="addon-link">
									<a href="<?php echo $addon->devotee_link ?>?utm_source=site&amp;utm_medium=acc_link_addon&amp;utm_campaign=monitor" class="available" target="_blank">
										View on devot:ee
										<?php if($addon->update_available) :
										// only show this icon if there is an available update ?>
										<span></span>
										<?php endif ?>
									</a>
								</td>
							<?php else : ?>
								<td class="addon-status">
									<a href="http://devot-ee.com/search/results/search&amp;keywords=<?php echo rawurlencode($addon->name) ?>&amp;channel=addons&amp;addon_version_support=ee2&amp;utm_source=site&amp;utm_medium=acc_link_search&amp;utm_campaign=monitor" target="_blank" class="warning">Not Found - Search devot:ee</a>
								</td>
								<td class="addon-link">
									&nbsp;
								</td>
							<?php endif ?>
						</tr>
						<?php if($addon->update_available && $addon->notes) : ?>
							<tr class="notes">
								<td class="notes" colspan="7">
									<h6>Release Notes</h6>
									<ul>
									<?php
										foreach (explode("\n", $addon->notes) as $note)
										{
											if ($note != '')
												echo "<li>$note</li>\n";
										}
									?>
									</ul>
								</td>
							</tr>
						<?php endif ?>
					<?php endforeach ?>
				</tbody>
			</table>
		<?php endif ?>

		<div id="devotee-footer">
			<p>Last Add-on Check: <?php echo date('l, M. j, Y @ g:ia.', $last_check) ?> <a href="<?php echo BASE . AMP . 'C=addons_accessories' . AMP . 'M=process_request' . AMP . 'accessory=devotee' . AMP . 'method=process_refresh' ?>" class="available refresh">Check Now</a></p>
			<p class="logos">
				<a href="http://devot-ee.com/?utm_source=site&amp;utm_medium=acc_link_logo&amp;utm_campaign=monitor" target="_blank" class="first">devot:ee</a>
				<a href="http://eecoder.com" target="_blank" class="last">eecoder</a>
			</p>
			<p>
				<small>
					EE Add-on Monitor is proudly powered by
					<a href="http://devot-ee.com?utm_source=site&amp;utm_medium=acc_link_title&amp;utm_campaign=monitor" target="_blank">devot:ee</a>
					in partnership with
					<a href="http://eecoder.com" target="_blank">eecoder</a>.
					Designed by
					<a href="http://antistaticdesign.com" target="_blank">Antistatic</a>
				</small>
			</p>
		</div>

	</div><!-- /.border -->
</div><!-- /.border -->