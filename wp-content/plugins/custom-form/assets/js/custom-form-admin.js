jQuery(document).ready(function($) {
    // fieldCount comes from PHP
    if (typeof fieldCount === 'undefined') {
        fieldCount = 0;
    }

    $('#addField-btn').on('click', function() {
        const fieldHtml = `
            <div class="form-field-wrapper">
                <div class="form-field-row" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
                    <input type="text" name="fields[${fieldCount}][label]" placeholder="Field Label" required>

                    <select name="fields[${fieldCount}][type]" class="field-type-select" required>
                        <option value="" selected>Choose type of Your field</option>
                        <option value="text">Text</option>
                        <option value="email">Email</option>
                        <option value="phone">Phone</option>
                        <option value="number">Number</option>
                        <option value="textarea">Textarea</option>
                        <option value="password">Password</option>
                        <option value="select">Select</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="radio">Radio</option>
                        <option value="button">Button</option>
                    </select>

                    <select name="fields[${fieldCount}][width]" required>
                        <option value="" selected>Choose width</option>
                        <option value="50%">Half (50%)</option>
                        <option value="100%">Full (100%)</option>
                    </select>

                    <label>
                        <input type="hidden" name="fields[${fieldCount}][required]" value="0">
                        <input type="checkbox" name="fields[${fieldCount}][required]" value="1"> Required
                    </label>

                    <button type="button" class="remove-field-btn">❌ Remove</button>
                </div>

                <div class="form-field-options-row" style="margin-top: 10px; width: 30%;">
                    <textarea name="fields[${fieldCount}][options]" class="field-options-textarea" 
                    placeholder="Enter one option per line" style="display: none; width: 100%; min-height: 80px;">
                    </textarea>
                </div>
            </div>
        `;

        $('#form-fields-container').append(fieldHtml);
        fieldCount++;
    });

    $('#form-fields-container').on('change', '.field-type-select', function() {
        const selectedType = $(this).val();
        const $optionsTextarea = $(this).closest('.form-field-wrapper').find('.field-options-textarea');

        if (selectedType === 'select' || selectedType === 'checkbox' || selectedType === 'radio') {
            $optionsTextarea.show();
        } else {
            $optionsTextarea.hide();
            $optionsTextarea.val('');
        }
    });

    $('#form-fields-container').on('click', '.remove-field-btn', function() {
        $(this).closest('.form-field-wrapper').remove();
    });
});
