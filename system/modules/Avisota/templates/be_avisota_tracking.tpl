<form action="contao/main.php?do=avisota_tracking" class="tl_form" method="post">
<div class="tl_formbody">
<input type="hidden" name="FORM_SUBMIT" value="tl_filters">

<div class="tl_panel">

<div class="tl_submit_panel tl_subpanel">
<input type="image" name="filter" id="filter" src="system/themes/default/images/reload.gif" class="tl_img_submit" title="<?php echo $GLOBALS['TL_LANG']['MSC']['apply']; ?>" value="<?php echo $GLOBALS['TL_LANG']['MSC']['apply']; ?>" />
</div>

<div class="tl_recipient tl_subpanel">
<strong><?php echo $GLOBALS['TL_LANG']['avisota_tracking']['recipient_label']; ?>:</strong>
<input type="text" id="recipient" name="recipient" class="tl_text" value="<?php echo specialchars($this->recipient); ?>" />
</div>

<div class="tl_newsletter tl_subpanel">
<strong><?php echo $GLOBALS['TL_LANG']['avisota_tracking']['newsletter_label']; ?>:</strong>
<select name="newsletter" class="tl_select" onchange="this.form.submit()">
<?php foreach ($this->newsletters as $id=>$newsletter): ?>
<option value="<?php echo $id; ?>"<?php if ($this->newsletter->id == $id): ?> selected="selected"<?php endif; ?>><?php echo $newsletter; ?></option>
<?php endforeach; ?>
</select>
</div>

<div class="clear"></div>

</div>
</div>
</form>

<div id="tl_buttons"></div>

<?php echo $this->getMessages(); ?>

<h2 class="sub_headline"><?php echo $GLOBALS['TL_LANG']['avisota_tracking']['headline']; ?></h2>

<script type="text/javascript" src="system/modules/Avisota/html/Number.js"></script>
<script type="text/javascript" src="system/modules/Avisota/html/functions.js"></script>
<script type="text/javascript" src="system/modules/Avisota/Meio.Autocomplete/<?php if (VERSION == 2.9): ?>1.0<?php else: ?>2.0<?php endif; ?>/Meio.Autocomplete.js"></script>
<script type="text/javascript" src="system/modules/Avisota/jquery/jquery.min.js"></script>
<script type="text/javascript">jQuery.noConflict();</script>
<script type="text/javascript" src="system/modules/Avisota/highstock/js/highstock.js"></script>

<div id="stats">
	<h3><?php echo $GLOBALS['TL_LANG']['avisota_tracking'][$this->mode]['stats_legend']; ?></h3>
	<div id="chart" style="position: relative; height: 420px; background: url(system/modules/Avisota/html/loading.gif) no-repeat center center;"></div>

	<table cellpadding="0" cellspacing="0" class="tl_listing">
		<colgroup>
			<col />
			<col />
			<col width="120" />
			<col width="120" />
		</colgroup>
		<tbody>
			<tr>
				<td class="tl_folder_tlist">&nbsp;</td>
				<td class="tl_folder_tlist tl_right_nowrap"><?php echo $GLOBALS['TL_LANG']['avisota_tracking']['col_sum']; ?></td>
				<td class="tl_folder_tlist tl_right_nowrap"><?php echo $GLOBALS['TL_LANG']['avisota_tracking']['col_percent']; ?></td>
				<td class="tl_folder_tlist tl_right_nowrap"><?php echo $GLOBALS['TL_LANG']['avisota_tracking']['col_percent2']; ?></td>
			</tr>
			<tr onmouseover="Theme.hoverRow(this, 1);" onmouseout="Theme.hoverRow(this, 0);">
				<td class="tl_file_list"><?php echo $GLOBALS['TL_LANG']['avisota_tracking'][$this->mode]['sends']; ?></td>
				<td class="tl_file_list tl_right_nowrap" id="col_sends">0</td>
				<td class="tl_file_list tl_right_nowrap" id="col_sends_percent"></td>
				<td class="tl_file_list tl_right_nowrap" id="col_sends_percent2"></td>
			</tr>
			<tr onmouseover="Theme.hoverRow(this, 1);" onmouseout="Theme.hoverRow(this, 0);">
				<td class="tl_file_list"><?php echo $GLOBALS['TL_LANG']['avisota_tracking'][$this->mode]['reads']; ?></td>
				<td class="tl_file_list tl_right_nowrap" id="col_reads"></td>
				<td class="tl_file_list tl_right_nowrap" id="col_reads_percent"></td>
				<td class="tl_file_list tl_right_nowrap" id="col_reads_percent2"></td>
			</tr>
			<tr onmouseover="Theme.hoverRow(this, 1);" onmouseout="Theme.hoverRow(this, 0);">
				<td class="tl_file_list"><?php echo $GLOBALS['TL_LANG']['avisota_tracking'][$this->mode]['reacts']; ?></td>
				<td class="tl_file_list tl_right_nowrap" id="col_reacts"></td>
				<td class="tl_file_list tl_right_nowrap" id="col_reacts_percent"></td>
				<td class="tl_file_list tl_right_nowrap" id="col_reacts_percent2"></td>
			</tr>
		</tbody>
	</table>
</div>
<br/><br/>

<div id="links">
	<h3><?php echo $GLOBALS['TL_LANG']['avisota_tracking'][$this->mode]['links_legend']; ?></h3>
	<div id="chart_links" style="position: relative; height: 420px; background: url(system/modules/Avisota/html/loading.gif) no-repeat center center;"></div>
	<table cellpadding="0" cellspacing="0" class="tl_listing">
		<colgroup>
			<col />
			<col />
			<col width="45" />
		</colgroup>
		<tbody>
			<tr>
				<td class="tl_folder_tlist"><?php echo $GLOBALS['TL_LANG']['avisota_tracking'][$this->mode]['url']; ?></td>
				<td class="tl_folder_tlist tl_right_nowrap"><?php echo $GLOBALS['TL_LANG']['avisota_tracking'][$this->mode]['hits']; ?></td>
				<td class="tl_folder_tlist"></td>
			</tr>
			<?php foreach ($this->links as $i=>$link): ?>
			<tr onmouseover="Theme.hoverRow(this, 1);" onmouseout="Theme.hoverRow(this, 0);">
				<td class="tl_file_list"><a href="<?php echo $link['url']; ?>" onclick="window.open(this.href); return false;"><?php echo $link['url']; ?></a></td>
				<td class="tl_file_list tl_right_nowrap"><?php echo number_format($link['hits'], 0, ',', '.'); ?></td>
				<td class="tl_file_list tl_right_nowrap"><?php echo $link['percent']; ?>&nbsp;%</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

<?php if (is_array($this->newsletter_reads)): ?>
<br/><br/>
<div id="newsletters">
	<h3><?php echo $GLOBALS['TL_LANG']['avisota_tracking'][$this->mode]['newsletters_legend']; ?></h3>
	<table cellpadding="0" cellspacing="0" class="tl_listing">
		<colgroup>
			<col width="16" />
			<col />
		</colgroup>
		<tbody>
			<tr>
				<td class="tl_folder_tlist">&nbsp;</td>
				<td class="tl_folder_tlist"><?php echo $GLOBALS['TL_LANG']['avisota_tracking'][$this->mode]['newsletter']; ?></td>
			</tr>
			<?php foreach ($this->newsletter_reads as $i=>$read): ?>
			<tr onmouseover="Theme.hoverRow(this, 1);" onmouseout="Theme.hoverRow(this, 0);">
				<td class="tl_file_list"><?php if ($read['readed']): ?><img src="system/modules/Avisota/html/outbox_sended.png" alt="<?php echo specialchars($GLOBALS['TL_LANG']['avisota_tracking'][$this->mode]['readed']); ?>" width="16" height="16" /><?php else: ?><img src="system/modules/Avisota/html/blank.gif" alt="" width="16" height="16" /><?php endif; ?></td>
				<td class="tl_file_list"><a href="contao/main.php?do=avisota_newsletter&table=tl_avisota_newsletter&key=send&id=<?php echo $read['id']; ?>"><?php echo $read['subject']; ?></a></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php endif; ?>

<script>
<?php if ($this->mode == 'recipient'): ?>var flags = false;
<?php endif; ?>
var sends = false;
var reads = false;
var reacts = false;
var links = false;

$(window).addEvent('domready', function() {
	new Meio.Autocomplete(
		$('recipient'),
		'contao/main.php?do=avisota_tracking&newsletter=<?php echo $this->newsletter['id']; ?>&data=recipients',
		{
			filter: {
				type: 'contains',
				path: 'text'
			}
		}
	);
});
$(window).addEvent('load', function() {
	if (sends || reads || reacts) return;

	function drawChart() {
		if (!sends || !reads || !reacts || !links<?php if ($this->mode == 'recipient'): ?> || !flags<?php endif; ?>) return;

		if (!sends.length && !reads.length && !reacts.length<?php if ($this->mode == 'recipient'): ?> && !flags.length<?php endif; ?>) {
			$('chart').setAttribute('style', '');
			$('chart')
				.addClass('empty_stats')
				.set('text', '<?php echo specialchars($GLOBALS['TL_LANG']['avisota_tracking']['empty_stats']); ?>');
		} else {
			var send = sends[sends.length-1][1];
			var read = reads[reads.length-1][1];
			var react = reacts[reacts.length-1][1];

			$('col_sends').set('text', send.formatNumber());
			$('col_reads').set('text', read.formatNumber());
			$('col_reads_percent').set('text', parseInt(read/send*100) + ' %');
			$('col_reacts').set('text', react.formatNumber());
			$('col_reacts_percent').set('text', parseInt(react/send*100) + ' %');
			$('col_reacts_percent2').set('text', parseInt(react/read*100) + ' %');

			<?php if ($this->mode == 'recipient'): ?>
			if (flags[0] && (!sends[0] || flags[0].x < sends[0][0])) {
				sends.splice(0, 0, [flags[0].x, 0]);
			}

			<?php endif; ?>
			equalize([sends, reads, reacts]);

			$('chart').setAttribute('style', '');

			var timespan = (sends[sends.length-1][0] - sends[0][0]) / 1000;

			new Highcharts.StockChart({
				chart: {
					renderTo: 'chart'
				},
				rangeSelector: {
					buttons: [{
						type: 'minute',
						count: 60,
						text: '1h'
					}, {
						type: 'minute',
						count: 6*60,
						text: '6h'
					}, {
						type: 'minute',
						count: 12*60,
						text: '12h'
					}, {
						type: 'day',
						count: 1,
						text: '1d'
					}, {
						type: 'day',
						count: 2,
						text: '2d'
					}, {
						type: 'day',
						count: 3,
						text: '3d'
					}, {
						type: 'week',
						count: 1,
						text: '1w'
					}, {
						type: 'month',
						count: 1,
						text: '1m'
					}, {
						type: 'all',
						text: 'All'
					}],
					selected: getRangeIndex(timespan)
				},
				yAxis: {
					min: 0,
					title: {
						text: ''
					}
				},
				tooltip: {
					formatter: function(){
						if (this.points) {
							var point = this.points[0];
							var series = point.series;
							var unit = series.unit && series.unit[0];
							var format = series.tooltipHeaderFormat;

							var s = '<b>' + Highcharts.dateFormat(format, this.x) + '</b>';
							for (var i=0; i<this.points.length; i++) {
								s += '<br/><b style="color:' + this.points[i].series.color + '">' + this.points[i].series.name + '</b>: ' + Highcharts.numberFormat(this.points[i].y, 0);
							}
							return s;
						} else {
							return this.series.data[0].text;
						}
					}
				},
				series: [{
					name: '<?php echo specialchars($GLOBALS['TL_LANG']['avisota_tracking'][$this->mode]['sends']); ?>',
					data: sends<?php if ($this->mode == 'recipient'): ?>,
					id: 'newsletter'<?php endif; ?>
				}, {
					name: '<?php echo specialchars($GLOBALS['TL_LANG']['avisota_tracking'][$this->mode]['reads']); ?>',
					data: reads
				}, {
					name: '<?php echo specialchars($GLOBALS['TL_LANG']['avisota_tracking'][$this->mode]['reacts']); ?>',
					data: reacts
				}<?php if ($this->mode == 'recipient'): ?>, {
					type: 'flags',
					data: flags,
					onSeries: 'newsletter',
					shape: 'circlepin',
					width: 16,
					cursor: 'pointer'
				}<?php endif; ?>]
			});
		}

		if (!links.length) {
			$('chart_links').setAttribute('style', '');
			$('chart_links')
				.addClass('empty_stats')
				.set('text', '<?php echo specialchars($GLOBALS['TL_LANG']['avisota_tracking']['empty_stats']); ?>');
		} else {
			<?php if ($this->mode == 'recipient'): ?>

			if (flags[0] && (!links[0].data[0] || flags[0].x < links[0].data[0][0])) {
				links[0].data.splice(0, 0, [flags[0].x, 0]);
			}

			<?php endif; ?>
			var link_arrays = [];
			$each(links, function(v) {
				link_arrays.push(v.data);
			});
			equalize(link_arrays);

			<?php if ($this->mode == 'recipient'): ?>

			links[0].id = 'first';
			links.push({
				type: 'flags',
				data: flags,
				onSeries: 'first',
				shape: 'circlepin',
				width: 16,
				cursor: 'pointer'
			});

			<?php endif; ?>
			$('chart_links').setAttribute('style', '');

			var timespan = (link_arrays[0][link_arrays[0].length-1][0] - link_arrays[0][0][0]) / 1000;

			new Highcharts.StockChart({
				chart: {
					renderTo: 'chart_links'
				},
				rangeSelector: {
					buttons: [{
						type: 'minute',
						count: 60,
						text: '1h'
					}, {
						type: 'minute',
						count: 6*60,
						text: '6h'
					}, {
						type: 'minute',
						count: 12*60,
						text: '12h'
					}, {
						type: 'day',
						count: 1,
						text: '1d'
					}, {
						type: 'day',
						count: 2,
						text: '2d'
					}, {
						type: 'day',
						count: 3,
						text: '3d'
					}, {
						type: 'week',
						count: 1,
						text: '1w'
					}, {
						type: 'month',
						count: 1,
						text: '1m'
					}, {
						type: 'all',
						text: 'All'
					}],
					selected: getRangeIndex(timespan)
				},
				yAxis: {
					min: 0,
					title: {
						text: ''
					}
				},
				tooltip: {
					formatter: function(){
						if (this.points) {
							var point = this.points[0];
							var series = point.series;
							var unit = series.unit && series.unit[0];
							var format = series.tooltipHeaderFormat;

							var s = '<b>' + Highcharts.dateFormat(format, this.x) + '</b>';
							for (var i=0; i<this.points.length; i++) {
								s += '<br/><strong style="color:' + this.points[i].series.color + '">' + this.points[i].series.name + '</strong>: ' + Highcharts.numberFormat(this.points[i].y, 0);
							}

							return s;
						} else {
							return this.series.data[0].text;
						}
					}
				},
				series: links
			});
		}
	}

	<?php if ($this->mode == 'recipient'): ?>
	new Request.JSON({
		url: 'contao/main.php?do=avisota_tracking&newsletter=<?php echo $this->newsletter['id']; ?>&recipient=<?php echo urlencode($this->recipient); ?>&data=flags',
		onComplete: function(json) {
			flags = json;
			drawChart();
		}
	}).get();

	<?php endif; ?>
	new Request.JSON({
		url: 'contao/main.php?do=avisota_tracking&newsletter=<?php echo $this->newsletter['id']; ?>&recipient=<?php echo urlencode($this->recipient); ?>&data=sends',
		onComplete: function(json) {
			sends = json;
			drawChart();
		}
	}).get();

	new Request.JSON({
		url: 'contao/main.php?do=avisota_tracking&newsletter=<?php echo $this->newsletter['id']; ?>&recipient=<?php echo urlencode($this->recipient); ?>&data=reads',
		onComplete: function(json) {
			reads = json;
			drawChart();
		}
	}).get();

	new Request.JSON({
		url: 'contao/main.php?do=avisota_tracking&newsletter=<?php echo $this->newsletter['id']; ?>&recipient=<?php echo urlencode($this->recipient); ?>&data=reacts',
		onComplete: function(json) {
			reacts = json;
			drawChart();
		}
	}).get();

	new Request.JSON({
		url: 'contao/main.php?do=avisota_tracking&newsletter=<?php echo $this->newsletter['id']; ?>&recipient=<?php echo urlencode($this->recipient); ?>&data=links',
		onComplete: function(json) {
			links = json;
			drawChart();
		}
	}).get();
});
</script>

<br>
