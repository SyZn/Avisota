
<table cellspacing="0" cellpadding="0" summary="Image gallery">
<tbody>
<?php foreach ($this->body as $class=>$row): ?>
<tr class="<?php echo $class; ?>">
<?php foreach ($row as $col): ?>
<?php if (!$col->addImage): ?>
  <td class="<?php echo $col->class; ?> empty">&nbsp;</td>
<?php else: ?>
  <td class="<?php echo $col->class; ?>" style="width:<?php echo $col->colWidth; ?>;">
  <div class="image_container"<?php if ($col->margin): ?> style="<?php echo $col->margin; ?>"<?php endif; ?>>
<?php if ($col->href): ?>
    <a href="<?php echo $col->href; ?>"<?php echo $col->attributes; ?> title="<?php echo $col->alt; ?>"><img src="<?php echo $col->src; ?>"<?php echo $col->imgSize; ?> alt="<?php echo $col->alt; ?>"></a>
<?php else: ?>
    <img src="<?php echo $col->src; ?>"<?php echo $col->imgSize; ?> alt="<?php echo $col->alt; ?>">
<?php endif; ?>
<?php if ($col->caption): ?>
    <div class="caption"><?php echo $col->caption; ?></div>
<?php endif; ?>
  </div>
  </td>
<?php endif; ?>
<?php endforeach; ?>
</tr>
<?php endforeach; ?>
</tbody>
</table>
