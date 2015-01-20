<?php foreach ($options as $key => $value): ?>
    <?php $id = $this->getFieldId() . '-' . htmlspecialchars($key) ?>
    <div class="radio">
        <label>
            <input type="radio" id="<?php echo $id ?>" name="<?php echo $this->getFieldName() ?>" value="<?php echo htmlspecialchars($key) ?>"<?php echo $key == $this->getValue() ? ' checked="checked"' : '' ?> />
            <?php echo $this->getLabelStyle($value) ?>
        </label>
    </div>
<?php endforeach ?>
