jQuery(document).ready(function($) {
    let wrapper = $('#answers-wrapper');
    let index = wrapper.find('.answer-row').length;


    $('#add-answer').on('click', function(e) {
        e.preventDefault();
        let html = `
            <div class="answer-row" style="margin-bottom:10px;padding:10px;border:1px solid #ddd;">
                <input type="text" name="answers[${index}][text]" placeholder="Answer Text" style="width:45%">
                <input type="hidden" class="image-field" name="answers[${index}][image]" value="">
                <button class="button upload-image">Upload Image</button>
                <img src="" style="max-width:50px; vertical-align:middle;display:none;">
                <input type="number" name="answers[${index}][points]" placeholder="Points" style="width:80px; margin-left:10px;">
               
                <button class="button remove-answer">Remove</button>
            </div>`;
        wrapper.append(html);
        index++;
    });


    $(document).on('click', '.upload-image', function(e) {
        e.preventDefault();
        let button = $(this);
        let imgField = button.siblings('.image-field');
        let imgTag = button.siblings('img');

        let frame = wp.media({
            title: 'Select Answer Image',
            button: { text: 'Use this image' },
            multiple: false
        });

        frame.on('select', function() {
            let attachment = frame.state().get('selection').first().toJSON();
            imgField.val(attachment.url);
            imgTag.attr('src', attachment.url).show();
        });

        frame.open();
    });

    $(document).on('click', '.remove-answer', function(e) {
        e.preventDefault();
        $(this).closest('.answer-row').remove();
    });
});




// jQuery(document).ready(function($) {
//     // initialize select2
//     function initSelect2() {
//         $('.form-step-select').select2({
//             placeholder: 'Select questions',
//             allowClear: true,
//             width: 'resolve'
//         });
//     }

//     initSelect2();


//     function getAvailableOptions() {
//         let options = [];
//         $('.form-step-select').first().find('option').each(function() {
//             options.push({
//                 id: $(this).val(),
//                 text: $(this).text()
//             });
//         });
//         return options;
//     }


//     function getSelectedQuestions() {
//         let selected = [];
//         $('.form-step-select').each(function() {
//             $(this).val()?.forEach(v => selected.push(v));
//         });
//         return selected;
//     }


//     function getFilteredOptions() {
//         let allOptions = getAvailableOptions();
//         let used = getSelectedQuestions();
//         return allOptions.filter(opt => !used.includes(opt.id));
//     }


//     $('#add-step').on('click', function() {
//         const wrapper = $('#form-steps-wrapper');
//         let newStepNum = wrapper.find('.form-step').length + 1;

//         let newStep = `
//             <div class="form-step" data-step="${newStepNum}">
//                 <h4>Step ${newStepNum}</h4>
//                 <select class="form-step-select" name="form_steps[${newStepNum}][]" multiple="multiple" style="width:100%;"></select>
//             </div>`;

//         wrapper.append(newStep);

//         let $select = wrapper.find('.form-step-select').last();
//         let filteredOptions = getFilteredOptions();

//         filteredOptions.forEach(opt => {
//             $select.append(new Option(opt.text, opt.id, false, false));
//         });

//         $select.select2({
//             placeholder: 'Select questions',
//             allowClear: true,
//             width: 'resolve'
//         });
//     });
// });

// jQuery(document).ready(function($) {

//     $(document).on('change', '#has_pre_inputs', function() {
//         if ($(this).is(':checked')) {
//             $('#pre-inputs-wrapper-main').show();
//         } else {
//             $('#pre-inputs-wrapper-main').hide();
//         }
//     });


//     $(document).on('click', '.add-pre-field', function(e) {
//         e.preventDefault();
//         let count = $('#pre-inputs-wrapper .pre-field').length;

//         let fieldHtml = `
//             <div class="pre-field"  style="margin: 10px 0px 20px 0px;">
//                 <input type="text" name="pre_fields[${count}][label]" placeholder="Field Label" style="margin: 0px 10px;"/>
//                 <select style="margin: 0px 10px;" name="pre_fields[${count}][field_type]">
//                     <option value="text">Text</option>
//                     <option value="email">Email</option>
//                     <option value="phone">Phone</option>
//                     <option value="textarea">Textarea</option>
//                 </select>
//                 <label style="margin: 0px 10px;"><input type="checkbox" name="pre_fields[${count}][required]" value="1" /> Required</label>
//                 <button  style="margin: 0px 10px;" type="button" class="button remove-pre-field">Remove</button>
//             </div>
//         `;

//         $('#pre-inputs-wrapper').append(fieldHtml);
//     });

//     $(document).on('click', '.remove-pre-field', function(e) {
//         e.preventDefault();
//         $(this).closest('.pre-field').remove();
//     });
// });

jQuery(document).ready(function($) {

    // Toggle pre-input wrapper
    $(document).on('change', '#has_pre_inputs', function() {
        if ($(this).is(':checked')) {
            $('#pre-inputs-wrapper-main').show();
        } else {
            $('#pre-inputs-wrapper-main').hide();
        }
    });

    // Add new field
    $(document).on('click', '.add-pre-field', function(e) {
        e.preventDefault();
        let count = $('#pre-inputs-wrapper .pre-field').length;

        // let fieldHtml = `
        // <div class="pre-field" style="margin: 10px 0px 20px 0px;">
        //     <input type="text" name="pre_fields[${count}][label]" placeholder="Field Label" style="margin: 0px 10px;"/>
        //     <select class="field-type" style="margin: 0px 10px;" name="pre_fields[${count}][field_type]">
        //         <option value="text">Text</option>
        //         <option value="email">Email</option>
        //         <option value="phone">Phone</option>
        //         <option value="textarea">Textarea</option>
        //         <option value="date">Date</option>
        //         <option value="select">Select</option>
        //         <option value="radio">Radio</option>
        //         <option value="checkbox">Checkbox</option>
        //     </select>
        //     <label style="margin: 0px 10px;">
        //         <input type="checkbox" name="pre_fields[${count}][required]" value="1" /> Required
        //     </label>
        //     <div class="field-options" style="display:none; margin:10px 0;">
        //         <textarea name="pre_fields[${count}][options]" placeholder="Enter options (one per line)" style="width:250px; height:80px;"></textarea>
        //     </div>
        //     <button style="margin: 0px 10px;" type="button" class="button remove-pre-field">Remove</button>
        // </div>
        // `;
        let fieldHtml = `
            <div class="pre-field" style="margin: 10px 0px 20px 0px;">
                <input type="text" name="pre_fields[${count}][label]" placeholder="Field Label" style="margin: 0px 10px;"/>
                <select class="field-type" style="margin: 0px 10px;" name="pre_fields[${count}][field_type]">
                    <option value="text">Text</option>
                    <option value="email">Email</option>
                    <option value="phone">Phone</option>
                    <option value="textarea">Textarea</option>
                    <option value="date">Date</option>
                    <option value="select">Select</option>
                    <option value="radio">Radio</option>
                    <option value="checkbox">Checkbox</option>
                </select>
                <select name="pre_fields[${count}][layout]" style="margin: 0px 10px;">
                    <option value="full">Full Width</option>
                    <option value="half">Half Width</option>
                    <option value="combine">Combine Width</option>
                </select>
                <label style="margin: 0px 10px;">
                    <input type="checkbox" name="pre_fields[${count}][required]" value="1" /> Required
                </label>
                <div class="field-options" style="display:none; margin:10px 0;">
                    <textarea name="pre_fields[${count}][options]" placeholder="Enter options (one per line)" style="width:250px; height:80px;"></textarea>
                </div>
                <button style="margin: 0px 10px;" type="button" class="button remove-pre-field">Remove</button>
            </div>
            `;


        $('#pre-inputs-wrapper').append(fieldHtml);
    });

    // Remove field
    $(document).on('click', '.remove-pre-field', function(e) {
        e.preventDefault();
        $(this).closest('.pre-field').remove();
    });

    // Show/hide options textarea when type changes
    $(document).on('change', '.field-type', function() {
        let val = $(this).val();
        let $options = $(this).closest('.pre-field').find('.field-options');
        if (['select','radio','checkbox'].includes(val)) {
            $options.show();
        } else {
            $options.hide();
        }
    });
});

jQuery(document).ready(function($) {
    // 1) Select2 init helper
    function initSelect2($ctx) {
        ($ctx || $(document)).find('.form-step-select').select2({
            placeholder: 'Select questions',
            allowClear: true,
            width: 'resolve'
        });
    }
    initSelect2();

    // 2) Capture a MASTER list of all options ONCE (union across all step selects)
    const MASTER_OPTIONS = (function buildMaster() {
        const seen = new Set();
        const out = [];
        $('.form-step-select option').each(function() {
            const id = String($(this).val());
            if (!seen.has(id)) {
                seen.add(id);
                out.push({ id, text: $(this).text() });
            }
        });
        return out;
    })();

    // 3) Get all selected IDs across all steps (as strings)
    function getSelectedQuestions() {
        const selected = [];
        $('.form-step-select').each(function() {
            const vals = $(this).val();
            if (Array.isArray(vals)) {
                vals.forEach(v => selected.push(String(v)));
            }
        });
        return selected;
    }

    let isRefreshing = false;

    // 4) Rebuild every select from MASTER, hiding used items in other steps
    function refreshAllSelects() {
        if (isRefreshing) return;
        isRefreshing = true;

        const used = getSelectedQuestions(); // strings

        $('.form-step-select').each(function() {
            const $select = $(this);
            const currentVals = ($select.val() || []).map(String);

            // Rebuild options from MASTER
            $select.empty();

            MASTER_OPTIONS.forEach(opt => {
                const isUsedElsewhere = used.includes(opt.id) && !currentVals.includes(opt.id);
                if (!isUsedElsewhere) {
                    // add; selected ONLY if it was already selected in this select
                    const selected = currentVals.includes(opt.id);
                    $select.append(new Option(opt.text, opt.id, selected, selected));
                }
            });

            // Tell select2 to refresh
            $select.trigger('change.select2');
        });

        isRefreshing = false;
    }
    // 8) Select All / Clear buttons
    $(document).on('click', '.select-all', function(e) {
        e.preventDefault();
        const $step = $(this).closest('.form-step');
        const $select = $step.find('.form-step-select');

        // Collect all option values in this select
        const allVals = $select.find('option').map(function() {
            return $(this).val();
        }).get();

        $select.val(allVals).trigger('change');
    });

    $(document).on('click', '.deselect-all', function(e) {
        e.preventDefault();
        const $step = $(this).closest('.form-step');
        const $select = $step.find('.form-step-select');

        $select.val([]).trigger('change');
    });


    // 5) When any selection changes, refresh all selects
    $(document).on('change', '.form-step-select', function() {
        refreshAllSelects();
    });

    // 6) Add new step
    $('#add-step').on('click', function() {
        const wrapper = $('#form-steps-wrapper');
        const newStepNum = wrapper.find('.form-step').length + 1;

        const newStep = `
            <div class="form-step" data-step="${newStepNum}">
                <h4>Step ${newStepNum}</h4>
                <select class="form-step-select" name="form_steps[${newStepNum}][]" multiple="multiple" style="width:100%;"></select>
            </div>`;
        wrapper.append(newStep);

        initSelect2(wrapper);
        refreshAllSelects(); // populate from MASTER minus used
    });

    // 7) First pass to enforce uniqueness on load
    refreshAllSelects();
});
document.addEventListener('DOMContentLoaded', function(){
    const radios = document.querySelectorAll('input[name="global_na_option"]');
    const customInput = document.querySelector('input[name="global_na_custom_points"]');

    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            customInput.disabled = (this.value !== 'minus_custom_points');
        });
    });
});