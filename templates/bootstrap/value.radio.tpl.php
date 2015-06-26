<?php

$notice = [];
if ($this->getElement('notice') != "") {
    $notice[] = $this->getElement('notice');
}
if (isset($this->params['warning_messages'][$this->getId()]) && !$this->params['hide_field_warning_messages']) {
    $notice[] =  '<span class="text-warning">' . rex_translate($this->params['warning_messages'][$this->getId()], null, false) . '</span>'; //    var_dump();
}
if (count($notice) > 0) {
    $notice = '<p class="help-block">' . implode("<br />", $notice) . '</p>';

} else {
    $notice = '';
}

foreach ($options as $key => $value): ?>
    <?php $id = $this->getFieldId() . '-' . htmlspecialchars($key) ?>
    <div class="radio">
        <label>
            <input type="radio" id="<?php echo $id ?>" name="<?php echo $this->getFieldName() ?>" value="<?php echo htmlspecialchars($key) ?>"<?php echo $key == $this->getValue() ? ' checked="checked"' : '' ?> />
            <?php echo $this->getLabelStyle($value) ?>
        </label>
    </div>
<?php endforeach ?>
<?php echo $notice; ?>
