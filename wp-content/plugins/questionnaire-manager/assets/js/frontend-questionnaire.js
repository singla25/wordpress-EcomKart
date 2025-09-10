// jQuery(document).ready(function($){
//     // var formId = $("#questionnaire_form").data("formid");
//     // var storageKey = "questionnaire_form_" + formId;
//     // var stepKey = "questionnaire_form_step_" + formId;

//     // // Show saved step if exists
//     // var savedStep = sessionStorage.getItem(stepKey);
//     // if(savedStep){
//     //     $("fieldset").hide(); 
//     //     $("fieldset").eq(savedStep).show(); 
//     // } else {
//     //     $("fieldset").hide(); 
//     //     $("fieldset").first().show(); 
//     // }

//     // // Restore saved form values
//     // var saved = sessionStorage.getItem(storageKey);
//     // if(saved){
//     //     var data = JSON.parse(saved);
//     //     $.each(data, function(name, entry){
//     //         var field = $("[name='"+name+"']");
//     //         if($.isArray(entry)){ // multiple values (checkbox group)
//     //             entry.forEach(function(v){
//     //                 field.filter("[value='"+v.value+"']").prop("checked", true);
//     //             });
//     //         } else {
//     //             if(field.attr("type") === "radio" || field.attr("type") === "checkbox"){
//     //                 field.filter("[value='"+entry.value+"']").prop("checked", true);
//     //             } else {
//     //                 field.val(entry.value);
//     //             }
//     //         }
//     //     });
//     //     var stepIndex = savedStep ? parseInt(savedStep) : 0;
//     //     var $currentFieldset = $("fieldset").eq(stepIndex);
//     //     calculateStepScores($currentFieldset);
//     // }

//     // var savedScores = JSON.parse(localStorage.getItem(scoreKey)) || {};
//     // $.each(savedScores, function(stepIndex, score){
//     //     var $fs = $("fieldset").eq(stepIndex);
//     //     $fs.find(".step-points").text(score.selectedPoints);
//     //     $fs.find(".step-possible").text(score.possiblePoints);
//     //     $fs.find(".step-percentage").text(score.percentage + "%");
//     // });

//     var formId = $("#questionnaire_form").data("formid");
//     var storageKey = "questionnaire_form_" + formId;
//     var stepKey = "questionnaire_form_step_" + formId;
//     var scoreKey = "questionnaire_scores_" + formId;

//     // ✅ Restore saved step
//     var savedStep = parseInt(sessionStorage.getItem(stepKey)) || 0;
//     $("fieldset").hide().eq(savedStep).show();

//     // ✅ Restore saved form values
//     var saved = sessionStorage.getItem(storageKey);
//     if(saved){
//         var data = JSON.parse(saved);
//         $.each(data, function(name, entry){
//             var field = $("[name='"+name+"']");
//             if($.isArray(entry)){
//                 entry.forEach(function(v){
//                     field.filter("[value='"+v.value+"']").prop("checked", true);
//                 });
//             } else {
//                 if(field.attr("type") === "radio" || field.attr("type") === "checkbox"){
//                     field.filter("[value='"+entry.value+"']").prop("checked", true);
//                 } else {
//                     field.val(entry.value);
//                 }
//             }
//         });
//     }

//     // ✅ Restore saved scores
//     var savedScores = JSON.parse(localStorage.getItem(scoreKey)) || {};
//     $.each(savedScores, function(stepIndex, score){
//         var $fs = $("fieldset").eq(stepIndex);
//         $fs.find(".step-points").text(score.selectedPoints);
//         $fs.find(".step-possible").text(score.possiblePoints);
//         $fs.find(".step-percentage").text(score.percentage + "%");
//     });

//     // NEXT BUTTON
//     $(".next").click(function(e){
//         e.preventDefault();
//         var current_fs = $(this).closest("fieldset");
//         var valid = true;

//         // clear old errors
//         current_fs.find(".error-message").remove();
//         current_fs.find(".error").removeClass("error");

//         current_fs.find("input, select, textarea").each(function(){
//             var field = $(this);
//             var val = field.val();
//             var type = field.attr("type");
//             var fieldName = field.attr("name");
        
//             if(field.prop("required")){
//                 if(type === "radio" || type === "checkbox"){
//                     // Only add error once per group
//                     if($("[name='" + fieldName + "']:checked").length === 0){
//                         if(field.closest(".field-warp-input").find(".error-message").length === 0){
//                             valid = false;
//                             field.closest(".field-warp-input").addClass("error");
//                             field.closest(".field-warp-input").append("<span class='error-message' style='color:red;font-size:12px;'>This field is required</span>");
//                         }
//                     }
//                 } else if(!val){
//                     valid = false;
//                     field.addClass("error");
//                     if(field.next(".error-message").length === 0){
//                         field.after("<span class='error-message' style='color:red;font-size:12px;'>This field is required</span>");
//                     }
//                 } else if(type === "email" && val){
//                     var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
//                     if(!emailRegex.test(val)){
//                         valid = false;
//                         field.addClass("error");
//                         if(field.next(".error-message").length === 0){
//                             field.after("<span class='error-message' style='color:red;font-size:12px;'>Enter a valid email</span>");
//                         }
//                     }
//                 } else if(type === "tel" && val){
//                     var phoneRegex = /^[0-9]{10}$/;
//                     if(!phoneRegex.test(val)){
//                         valid = false;
//                         field.addClass("error");
//                         if(field.next(".error-message").length === 0){
//                             field.after("<span class='error-message' style='color:red;font-size:12px;'>Enter a valid 10-digit phone number</span>");
//                         }
//                     }
//                 }
//             }
//         });
        
//         if(!valid) return false;

//         // show next step
//         var next_fs = current_fs.next();
//         next_fs.show();
//         current_fs.hide();

//         // Save current step index
//           // Save current step index
//         sessionStorage.setItem(stepKey, $("fieldset").index(next_fs));

//         // ✅ If summary step, populate totals
//         if(next_fs.hasClass("summary-step")){
//             var savedScores = JSON.parse(localStorage.getItem(scoreKey)) || {};
//             var totalSelected = 0, totalPossible = 0;

//             $.each(savedScores, function(i, s){
//                 totalSelected += parseFloat(s.selectedPoints);
//                 totalPossible += parseFloat(s.possiblePoints);
//             });

//             var overallPercentage = totalPossible > 0 ? ((totalSelected / totalPossible) * 100).toFixed(2) : "0.00";

//             // Fill summary inputs
//             $("#overall_score").val(totalSelected);
//             $("#overall_percentage").val(overallPercentage + "%");

//             // Example rank logic (adjust as needed)
//             var rank = "Bronze";
//             if(overallPercentage >= 90) rank = "Platinum";
//             else if(overallPercentage >= 80) rank = "Gold";
//             else if(overallPercentage >= 67) rank = "Silver";
//             else rank = "Bronze";

//             $("#rank_achieved").val(rank);
//         }
//     });

//     // PREVIOUS BUTTON
//     $(".previous").click(function(){
//         var current_fs = $(this).closest("fieldset");
//         var previous_fs = current_fs.prev();
//         previous_fs.show();
//         current_fs.hide();

//         // Save current step index
//         sessionStorage.setItem(stepKey, $("fieldset").index(previous_fs));
//     });

//     // SUBMIT BUTTON
//     $(".submit").click(function(e){
//         e.preventDefault();
//         var formData = {};
//         var scoreKey = "questionnaire_scores_" + formId;
//         var savedScores = JSON.parse(localStorage.getItem(scoreKey)) || {};
//         var totalSelected = 0, totalPossible = 0;
    
//         $.each(savedScores, function(i, s){
//             totalSelected += parseFloat(s.selectedPoints);
//             totalPossible += parseFloat(s.possiblePoints);
//         });
    
//         var overallPercentage = totalPossible > 0 ? ((totalSelected / totalPossible) * 100).toFixed(2) : "0.00";

//         $("#questionnaire_form").find("input, select, textarea").each(function(){
//             var type = $(this).attr("type");
//             if(type === "button" || type === "submit") return; // 🚫 skip navigation buttons

//             var name = $(this).attr("name");
//             if(!name) return;

//             var entry = {
//                 value: null,
//                 points: 0,
//                 is_na: 0,
//                 price: 0,
//                 q_id: 0,
//                 category: 0,
//                 type: null
//             };

//             if($(this).is(":checkbox")){
//                 entry.type = "checkbox";
//                 if(!formData[name]) formData[name] = [];
//                 if($(this).is(":checked")){
//                     entry.value = $(this).val();
//                     entry.points = parseInt($(this).data("points")) || 0;
//                     entry.is_na = parseInt($(this).data("is-na")) || 0;
//                     entry.is_pre_field = parseFloat($(this).data("is-pre_field")) || 0;
//                     entry.q_id = parseFloat($(this).data("id")) || 0;
//                     entry.category = parseFloat($(this).data("category")) || "";
//                     formData[name].push(entry);
//                 }
//             } else if($(this).is(":radio")){
//                 entry.type = "radio";
//                 if($(this).is(":checked")){
//                     entry.value = $(this).val();
//                     entry.points = parseInt($(this).data("points")) || 0;
//                     entry.is_na = parseInt($(this).data("is-na")) || 0;
//                     entry.is_pre_field = parseFloat($(this).data("is-pre_field")) || 0;
//                     entry.q_id = parseFloat($(this).data("id")) || 0;
//                     entry.category = parseFloat($(this).data("category")) || "";
//                     formData[name] = entry;
//                 }
//             } else if($(this).is("select")){
//                 entry.type = "select";
//                 var $opt = $(this).find("option:selected");
//                 entry.value = $opt.val();
//                 entry.points = parseInt($opt.data("points")) || 0;
//                 entry.is_na = parseInt($opt.data("is-na")) || 0;
//                 entry.is_pre_field = parseFloat($(this).data("is-pre_field")) || 0;
//                 entry.q_id = parseFloat($(this).data("id")) || 0;
//                 entry.category = parseFloat($(this).data("category")) || "";
//                 formData[name] = entry;
//             } else {
//                 entry.type = $(this).attr("type") || "text"; 
//                 entry.value = $(this).val();
//                 entry.points = parseInt($(this).data("points")) || 0;
//                 entry.is_na = parseInt($(this).data("is-na")) || 0;
//                 entry.is_pre_field = parseFloat($(this).data("is-pre_field")) || 0;
//                 entry.q_id = parseFloat($(this).data("id")) || 0;
//                 entry.category = parseFloat($(this).data("category")) || "";
//                 formData[name] = entry;
//             }
//         });

//         // // calculate totals
//         // var totalPoints = 0, totalPrice = 0;
//         // $.each(formData, function(name, entry){
//         //     if($.isArray(entry)){
//         //         entry.forEach(function(e){
//         //             totalPoints += e.points;
//         //             totalPrice += e.price;
//         //         });
//         //     } else {
//         //         totalPoints += entry.points;
//         //         totalPrice += entry.price;
//         //     }
//         // });

//         // console.log("Final Form Data:", formData);
//         // console.log("Total Points:", totalPoints);
//         // calculate totals
//             var totalPoints = 0, totalPrice = 0;
//             $.each(formData, function(name, entry){
//                 if($.isArray(entry)){
//                     entry.forEach(function(e){
//                         if(e.is_na !== 1){ // 🚫 exclude NA
//                             totalPoints += e.points;
//                             totalPrice += e.price;
//                         }
//                     });
//                 } else {
//                     if(entry.is_na !== 1){ // 🚫 exclude NA
//                         totalPoints += entry.points;
//                         totalPrice += entry.price;
//                     }
//                 }
//             });

//             var overallPercentage = totalPoints > 0 ? ((totalPoints / totalPossible) * 100).toFixed(2) : "0.00";

//             $("#overall_score").val(totalPoints);
//             $("#overall_percentage").val(overallPercentage + "%");
//             var rank = "Bronze";
//             if(overallPercentage >= 90) rank = "Platinum";
//             else if(overallPercentage >= 80) rank = "Gold";
//             else if(overallPercentage >= 67) rank = "Silver";
//             else rank = "Bronze";

//             $("#rank_achieved").val(rank); // same rank logic as above
            


//         $.post(qm_ajax.ajaxurl, {
//             action: "submit_questionnaire",
//             form_id: formId,
//             form_data: formData,
//             total_points: totalPoints,
//             overall_percentage: overallPercentage,
//             overall_rank: rank
//         }, function(res){
//             if(res.success){
//                 sessionStorage.removeItem(storageKey);
//                 sessionStorage.removeItem(stepKey);
//                 localStorage.removeItem(scoreKey); //
//                 var thankyouHtml = res.data.thankyou_content || '';
//                 if(res.data.pdf_url){
//                     thankyouHtml = thankyouHtml.replace(/!pdf_url!/g, res.data.pdf_url);
//                 }
        
//                 Swal.fire('Success', res.data.message, 'success').then(()=>{
//                     $("#questionnaire_form")[0].reset();
//                     $("#questionnaire_form").hide(); // hide form
//                     $("#thankyou-container").html(thankyouHtml).fadeIn();
//                 });
//             } else {
//                 Swal.fire('Error', res.data.message, 'error');
//             }
//         }, 'json').fail(function(){
//             Swal.fire('Error', 'Something went wrong', 'error');
//         });
//     });

//     $("#questionnaire_form").find("input, select, textarea").on("input change", function(){
//         var formData = {};
//         $("#questionnaire_form").find("input, select, textarea").each(function(){
//             var name = $(this).attr("name");
//             if(!name) return;

//             var entry = {
//                 value: null,
//                 points: 0,
//                 is_pre_field: 0,
//                 q_id: 0,
//                 category: 0,
//                 is_na: 0,
//                 price: 0

//             };

//             if($(this).is(":checkbox")){
//                 if(!formData[name]) formData[name] = [];
//                 if($(this).is(":checked")){
//                     entry.value = $(this).val();
//                     entry.points = parseInt($(this).data("points")) || 0;
//                     entry.is_na = parseInt($(this).data("is-na")) || 0;
//                     entry.is_pre_field = parseFloat($(this).data("is-pre_field")) || 0;
//                     entry.q_id = parseFloat($(this).data("id")) || 0;
//                     entry.category = parseFloat($(this).data("category")) || "";
//                     formData[name].push(entry);
//                 }
//             } else if($(this).is(":radio")){
//                 if($(this).is(":checked")){
//                     entry.value = $(this).val();
//                     entry.points = parseInt($(this).data("points")) || 0;
//                     entry.is_na = parseInt($(this).data("is-na")) || 0;
//                     entry.is_pre_field = parseFloat($(this).data("is-pre_field")) || 0;
//                     entry.q_id = parseFloat($(this).data("id")) || 0;
//                     entry.category = parseFloat($(this).data("category")) || "";
//                     formData[name] = entry;
//                 }
//             } else if($(this).is("select")){
//                 var $opt = $(this).find("option:selected");
//                 entry.value = $opt.val();
//                 entry.points = parseInt($opt.data("points")) || 0;
//                 entry.is_na = parseInt($opt.data("is-na")) || 0;
//                 entry.is_pre_field = parseFloat($(this).data("is-pre_field")) || 0;
//                 entry.q_id = parseFloat($(this).data("id")) || 0;
//                 entry.category = parseFloat($(this).data("category")) || "";
//                 formData[name] = entry;
//             } else {
//                 entry.value = $(this).val();
//                 entry.points = parseInt($(this).data("points")) || 0;
//                 entry.is_na = parseInt($(this).data("is-na")) || 0;
//                 entry.is_pre_field = parseFloat($(this).data("is-pre_field")) || 0;
//                 entry.q_id = parseFloat($(this).data("id")) || 0;
//                 entry.category = parseFloat($(this).data("category")) || "";
//                 formData[name] = entry;
//             }
//         });
//         sessionStorage.setItem(storageKey, JSON.stringify(formData));
//     });

//     $("#questionnaire_form").on("change input", "input, select", function(){
//         var $fieldset = $(this).closest("fieldset");
//         calculateStepScores($fieldset);
//     });
    
//     $(".next, .previous").click(function(){
//         var $fieldset = $(this).closest("fieldset");
//         calculateStepScores($fieldset);
//     });
    
// // function calculateStepScores($fieldset) {
// //     var selectedPoints = 0;
// //     var possiblePoints = 0;
// //     var grouped = {};

// //     // Group inputs by name
// //     $fieldset.find("input[type='radio'], input[type='checkbox'], select").each(function() {
// //         var $input = $(this);
// //         var name = $input.attr("name");
// //         if (!grouped[name]) {
// //             grouped[name] = [];
// //         }
// //         grouped[name].push($input);
// //     });

// //     // Loop through each group
// //     $.each(grouped, function(name, inputs) {
// //         var maxPoints = 0;

// //         inputs.forEach(function($input) {
// //             var points = 0;
// //             var isNa = false;

// //             if ($input.is("select")) {
// //                 $input.find("option").each(function() {
// //                     var $opt = $(this);
// //                     if (parseInt($opt.data("is-na")) !== 1) {
// //                         var optPoints = parseInt($opt.data("points")) || 0;
// //                         if (optPoints > maxPoints) maxPoints = optPoints;
// //                     }
// //                 });

// //                 var $selected = $input.find("option:selected");
// //                 if (parseInt($selected.data("is-na")) !== 1) {
// //                     selectedPoints += parseInt($selected.data("points")) || 0;
// //                 }

// //             } else {
// //                 isNa = parseInt($input.data("is-na")) === 1;
// //                 points = parseInt($input.data("points")) || 0;

// //                 // Possible points
// //                 if (!isNa) {
// //                     if ($input.attr("type") === "checkbox") {
// //                         possiblePoints += points;
// //                     } else if ($input.attr("type") === "radio") {
// //                         if (points > maxPoints) maxPoints = points;
// //                     }
// //                 }

// //                 // Selected points
// //                 if ($input.is(":checked") && !isNa) {
// //                     selectedPoints += points;
// //                 }
// //             }
// //         });

// //         // For radio/select groups: add max to possible
// //         if (inputs[0].attr("type") === "radio" || inputs[0].is("select")) {
// //             possiblePoints += maxPoints;
// //         }
// //     });

// //     // Update DOM
// //     var percentage = possiblePoints > 0 ? ((selectedPoints / possiblePoints) * 100).toFixed(2) : "0.00";
// //     $fieldset.find(".step-points").text(selectedPoints);
// //     $fieldset.find(".step-possible").text(possiblePoints);
// //     $fieldset.find(".step-percentage").text(percentage + "%");
// // }

// // });
// function calculateStepScores($fieldset) {
//     var selectedPoints = 0;
//     var possiblePoints = 0;
//     var grouped = {};

//     $fieldset.find("input[type='radio'], input[type='checkbox'], select").each(function() {
//         var $input = $(this);
//         var name = $input.attr("name");
//         if (!grouped[name]) grouped[name] = [];
//         grouped[name].push($input);
//     });

//     $.each(grouped, function(name, inputs) {
//         var maxPoints = 0;
//         inputs.forEach(function($input) {
//             var points = parseInt($input.data("points")) || 0;
//             var isNa = parseInt($input.data("is-na")) === 1;

//             if ($input.is("select")) {
//                 $input.find("option").each(function() {
//                     var $opt = $(this);
//                     if (parseInt($opt.data("is-na")) !== 1) {
//                         var optPoints = parseInt($opt.data("points")) || 0;
//                         if (optPoints > maxPoints) maxPoints = optPoints;
//                     }
//                 });

//                 var $selected = $input.find("option:selected");
//                 if ($selected.length && parseInt($selected.data("is-na")) !== 1) {
//                     selectedPoints += parseInt($selected.data("points")) || 0;
//                 }

//             } else {
//                 if (!isNa) {  // 🚫 Skip NA
//                     if ($input.attr("type") === "checkbox") {
//                         possiblePoints += points;
//                     } else if ($input.attr("type") === "radio") {
//                         if (points > maxPoints) maxPoints = points;
//                     }
//                 }
//                 if ($input.is(":checked") && !isNa) {
//                     selectedPoints += points;
//                 }
//             }
//         });
//         if ((inputs[0].attr("type") === "radio" || inputs[0].is("select")) && maxPoints > 0) {
//             possiblePoints += maxPoints;
//         }
//     });

//     var percentage = possiblePoints > 0 ? ((selectedPoints / possiblePoints) * 100).toFixed(2) : "0.00";
//     $fieldset.find(".step-points").text(selectedPoints);
//     $fieldset.find(".step-possible").text(possiblePoints);
//     $fieldset.find(".step-percentage").text(percentage + "%");

//     // ✅ Save scores
//     var formId = $("#questionnaire_form").data("formid");
//     var scoreKey = "questionnaire_scores_" + formId;
//     var index = $("fieldset").index($fieldset);
//     var savedScores = JSON.parse(localStorage.getItem(scoreKey)) || {};
//     savedScores[index] = { selectedPoints, possiblePoints, percentage };
//     localStorage.setItem(scoreKey, JSON.stringify(savedScores));
// }

// });




jQuery(document).ready(function($){

    // 🔹 Keys
    var formId    = $("#questionnaire_form").data("formid");
    var storageKey= "questionnaire_form_" + formId;
    var stepKey   = "questionnaire_form_step_" + formId;
    var scoreKey  = "questionnaire_scores_" + formId;

    // ==========================================================
    // 🔹 Restore Step & Values
    // ==========================================================
    var savedStep = parseInt(sessionStorage.getItem(stepKey)) || 0;
    $("fieldset").hide().eq(savedStep).show();

    var saved = sessionStorage.getItem(storageKey);
    if(saved){
        restoreFormValues(JSON.parse(saved));
    }

    // Restore scores
    var savedScores = JSON.parse(localStorage.getItem(scoreKey)) || {};
    $.each(savedScores, function(stepIndex, score){
        var $fs = $("fieldset").eq(stepIndex);
        updateStepUI($fs, score.selectedPoints, score.possiblePoints, score.percentage);
    });

    // ==========================================================
    // 🔹 Next Button
    // ==========================================================
    $(".next").click(function(e){
        e.preventDefault();
        var $current = $(this).closest("fieldset");

        var { valid } = validateAndCollect($current);
        if(!valid) return false;

        var $next = $current.next();
        $next.show(); $current.hide();

        sessionStorage.setItem(stepKey, $("fieldset").index($next));

        // If summary step → populate totals
        if($next.hasClass("summary-step")){
            populateSummary();
        }
    });

    // ==========================================================
    // 🔹 Previous Button
    // ==========================================================
    $(".previous").click(function(){
        var $current = $(this).closest("fieldset");
        var $prev = $current.prev();
        $prev.show(); $current.hide();
        sessionStorage.setItem(stepKey, $("fieldset").index($prev));
    });

    // ==========================================================
    // 🔹 Submit Button
    // ==========================================================
    // $(".submit").click(function(e){
    //     e.preventDefault();

    //     var { formData } = validateAndCollect($("#questionnaire_form"));

    //     // Calculate totals
    //     var totalPoints = 0, totalPrice = 0;
    //     $.each(formData, function(name, entry){
    //         if($.isArray(entry)){
    //             entry.forEach(function(e){
    //                 if(e.is_na !== 1){
    //                     totalPoints += e.points;
    //                     totalPrice  += e.price;
    //                 }
    //             });
    //         } else {
    //             if(entry.is_na !== 1){
    //                 totalPoints += entry.points;
    //                 totalPrice  += entry.price;
    //             }
    //         }
    //     });

    //     // Calculate rank
    //     var savedScores = JSON.parse(localStorage.getItem(scoreKey)) || {};
    //     var totalSelected = 0, totalPossible = 0;
    //     $.each(savedScores, function(i, s){
    //         totalSelected += parseFloat(s.selectedPoints);
    //         totalPossible += parseFloat(s.possiblePoints);
    //     });
    //     var overallPercentage = totalPossible > 0 ? ((totalSelected / totalPossible) * 100).toFixed(2) : "0.00";
    //     var rank = getRank(overallPercentage);

    //     $("#overall_score").val(totalPoints);
    //     $("#overall_percentage").val(overallPercentage + "%");
    //     $("#rank_achieved").val(rank);

    //     // Ajax submit
    //     $.post(qm_ajax.ajaxurl, {
    //         action: "submit_questionnaire",
    //         form_id: formId,
    //         form_data: formData,
    //         total_points: totalPoints,
    //         overall_percentage: overallPercentage,
    //         overall_rank: rank
    //     }, function(res){
    //         if(res.success){
    //             sessionStorage.removeItem(storageKey);
    //             sessionStorage.removeItem(stepKey);
    //             localStorage.removeItem(scoreKey);
    //             var thankyouHtml = res.data.thankyou_content || '';
    //             if(res.data.pdf_url){
    //                 thankyouHtml = thankyouHtml.replace(/!pdf_url!/g, res.data.pdf_url);
    //             }
    //             Swal.fire('Success', res.data.message, 'success').then(()=>{
    //                 $("#questionnaire_form")[0].reset().hide();
    //                 $("#thankyou-container").html(thankyouHtml).fadeIn();
    //             });
    //         } else {
    //             Swal.fire('Error', res.data.message, 'error');
    //         }
    //     }, 'json').fail(function(){
    //         Swal.fire('Error', 'Something went wrong', 'error');
    //     });
    // });
    $(".submit").click(function(e){
        e.preventDefault();
    
        var { formData } = validateAndCollect($("#questionnaire_form"));
    
        // Calculate totals
        var totalPoints = 0, totalPrice = 0;
        $.each(formData, function(name, entry){
            if($.isArray(entry)){
                entry.forEach(function(e){
                    if(e.is_na !== 1){
                        totalPoints += e.points;
                        totalPrice  += e.price;
                    }
                });
            } else {
                if(entry.is_na !== 1){
                    totalPoints += entry.points;
                    totalPrice  += entry.price;
                }
            }
        });
    
        // Calculate rank
        var savedScores = JSON.parse(localStorage.getItem(scoreKey)) || {};
        var totalSelected = 0, totalPossible = 0;
    
        // 🔹 Collect step data with titles
        var stepsData = [];
        $("fieldset").each(function(i){
            var $fs = $(this);
            var title = $fs.find(".step-title").text().trim(); // step_title
            var discriptions = $fs.find(".step-discriptions").text().trim(); // step_title
            
            var score = savedScores[i] || { selectedPoints: 0, possiblePoints: 0, percentage: "0.00" };
    
            stepsData.push({
                step_index: i,
                step_title: title || "Step " + (i+1),
                step_discriptions: discriptions || "",
                step_points: score.selectedPoints,
                step_percentage: score.percentage
            });
        });
    
        $.each(savedScores, function(i, s){
            totalSelected += parseFloat(s.selectedPoints);
            totalPossible += parseFloat(s.possiblePoints);
        });
    
        var overallPercentage = totalPossible > 0 ? ((totalSelected / totalPossible) * 100).toFixed(2) : "0.00";
        var rank = getRank(overallPercentage);
    
        $("#overall_score").val(totalPoints);
        $("#overall_percentage").val(overallPercentage + "%");
        $("#rank_achieved").val(rank);
    
        // 🔹 Ajax submit including step data
        $.post(qm_ajax.ajaxurl, {
            action: "submit_questionnaire",
            security: qm_ajax.security, 
            form_id: formId,
            form_data: formData,
            total_points: totalPoints,
            overall_percentage: overallPercentage,
            overall_rank: rank,
            steps_data: stepsData   // ✅ added
        }, function(res){
            if(res.success){
                sessionStorage.removeItem(storageKey);
                sessionStorage.removeItem(stepKey);
                localStorage.removeItem(scoreKey);
                var thankyouHtml = res.data.thankyou_content || '';
                if(res.data.pdf_url){
                    thankyouHtml = thankyouHtml.replace(/!pdf_url!/g, res.data.pdf_url);
                }
                Swal.fire('Success', res.data.message, 'success').then(()=>{
                    $("#questionnaire_form")[0].reset().hide();
                    $("#thankyou-container").html(thankyouHtml).fadeIn();
                });
            } else {
                Swal.fire('Error', res.data.message, 'error');
            }
        }, 'json').fail(function(){
            Swal.fire('Error', 'Something went wrong', 'error');
        });
    });
    
    // ==========================================================
    // 🔹 Auto Save on Input Change
    // ==========================================================
    $("#questionnaire_form").on("input change", "input, select, textarea", function(){
        var { formData } = validateAndCollect($("#questionnaire_form"), false);
        sessionStorage.setItem(storageKey, JSON.stringify(formData));

        var $fieldset = $(this).closest("fieldset");
        calculateStepScores($fieldset);
    });

    $(".next, .previous").click(function(){
        var $fieldset = $(this).closest("fieldset");
        calculateStepScores($fieldset);
    });

    // ==========================================================
    // 🔹 Helper Functions
    // ==========================================================

    function validateAndCollect($fieldset){
        var valid = true;
        var formData = {};
    
        $fieldset.find("input, select, textarea").each(function(){
            var field = $(this);
            var val   = $.trim(field.val());
            var type  = field.attr("type");
            var name  = field.attr("name");
            if(!name) return;
    
            // Clear old errors
            field.removeClass("error");
            field.next(".error-message").remove();
    
            // ---------- REQUIRED (must not be empty) ----------
            if(field.prop("required")){
                if((type === "radio" || type === "checkbox") && $("[name='"+name+"']:checked").length === 0){
                    valid = false;
                    if(field.closest(".field-warp-input").find(".error-message").length === 0){
                        field.closest(".field-warp-input").append("<span class='error-message' style='color:red;font-size:12px;'>This field is required</span>");
                    }
                    return; 
                }
                if(!val){
                    valid = false;
                    field.addClass("error").after("<span class='error-message' style='color:red;font-size:12px;'>This field is required</span>");
                    return;
                }
            }
    
            // ---------- FORMAT CHECK (always if value exists) ----------
            if(val){
                // EMAIL
                if(type === "email"){
                    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if(!emailRegex.test(val)){
                        valid = false;
                        field.addClass("error").after("<span class='error-message' style='color:red;font-size:12px;'>Enter a valid email address</span>");
                    }
                }
    
                // PHONE
                if(type === "tel" || field.hasClass("phone")){
                    var phoneRegex = /^[0-9]{10}$/;
                    if(!phoneRegex.test(val)){
                        valid = false;
                        field.addClass("error").after("<span class='error-message' style='color:red;font-size:12px;'>Enter a valid 10-digit phone number</span>");
                    }
                }
    
                // DATE
                if(type === "date"){
                    var dateRegex = /^\d{4}-\d{2}-\d{2}$/; // YYYY-MM-DD
                    if(!dateRegex.test(val)){
                        valid = false;
                        field.addClass("error").after("<span class='error-message' style='color:red;font-size:12px;'>Enter date in YYYY-MM-DD format</span>");
                    }
                }
    
                // NUMBER
                if(type === "number"){
                    if(isNaN(val)){
                        valid = false;
                        field.addClass("error").after("<span class='error-message' style='color:red;font-size:12px;'>Enter a valid number</span>");
                    }
                }
            }
    
            // ---------- COLLECT DATA ----------
            var entry = {
                value: field.val(),
                points: parseInt(field.data("points")) || 0,
                is_na: parseInt(field.data("is-na")) || 0,
                is_pre_field: parseInt(field.data("is-pre_field")) || 0,
                q_id: parseInt(field.data("id")) || 0,
                category: field.data("category") || "",
                type: type || "text"
            };
    
            if(field.is(":checkbox")){
                if(!formData[name]) formData[name] = [];
                if(field.is(":checked")) formData[name].push(entry);
            } else if(field.is(":radio")){
                if(field.is(":checked")) formData[name] = entry;
            } else if(field.is("select")){
                var $opt = field.find("option:selected");
                entry.value = $opt.val();
                entry.points = parseInt($opt.data("points")) || 0;
                entry.is_na = parseInt($opt.data("is-na")) || 0;
                formData[name] = entry;
            } else {
                formData[name] = entry;
            }
        });
    
        return { valid, formData };
    }
    
    

    function restoreFormValues(data){
        $.each(data, function(name, entry){
            var field = $("[name='"+name+"']");
            if($.isArray(entry)){
                entry.forEach(function(v){
                    field.filter("[value='"+v.value+"']").prop("checked", true);
                });
            } else {
                if(field.attr("type") === "radio" || field.attr("type") === "checkbox"){
                    field.filter("[value='"+entry.value+"']").prop("checked", true);
                } else {
                    field.val(entry.value);
                }
            }
        });
    }

    function populateSummary(){
        var savedScores = JSON.parse(localStorage.getItem(scoreKey)) || {};
        var totalSelected = 0, totalPossible = 0;

        $.each(savedScores, function(i, s){
            totalSelected += parseFloat(s.selectedPoints);
            totalPossible += parseFloat(s.possiblePoints);
        });

        var overallPercentage = totalPossible > 0 ? ((totalSelected / totalPossible) * 100).toFixed(2) : "0.00";
        $("#overall_score").val(totalSelected);
        $("#overall_percentage").val(overallPercentage + "%");
        $("#rank_achieved").val(getRank(overallPercentage));
    }

    function getRank(pct){
        if(pct >= 90) return "Platinum";
        if(pct >= 80) return "Gold";
        if(pct >= 67) return "Silver";
        return "Bronze";
    }

    function updateStepUI($fieldset, selectedPoints, possiblePoints, percentage){
        $fieldset.find(".step-points").text(selectedPoints);
        $fieldset.find(".step-possible").text(possiblePoints);
        $fieldset.find(".step-percentage").text(percentage + "%");
    }

    function calculateStepScores($fieldset){
        var selectedPoints = 0, possiblePoints = 0, grouped = {};

        $fieldset.find("input[type='radio'], input[type='checkbox'], select").each(function(){
            var $input = $(this);
            var name = $input.attr("name");
            if (!grouped[name]) grouped[name] = [];
            grouped[name].push($input);
        });

        $.each(grouped, function(name, inputs){
            var maxPoints = 0;
            inputs.forEach(function($input){
                var points = parseInt($input.data("points")) || 0;
                var isNa   = parseInt($input.data("is-na")) === 1;

                if ($input.is("select")) {
                    $input.find("option").each(function(){
                        var $opt = $(this);
                        if(parseInt($opt.data("is-na")) !== 1){
                            var optPoints = parseInt($opt.data("points")) || 0;
                            if(optPoints > maxPoints) maxPoints = optPoints;
                        }
                    });
                    var $selected = $input.find("option:selected");
                    if($selected.length && parseInt($selected.data("is-na")) !== 1){
                        selectedPoints += parseInt($selected.data("points")) || 0;
                    }
                } else {
                    if(!isNa){
                        if($input.attr("type") === "checkbox"){
                            possiblePoints += points;
                        } else if($input.attr("type") === "radio"){
                            if(points > maxPoints) maxPoints = points;
                        }
                    }
                    if($input.is(":checked") && !isNa){
                        selectedPoints += points;
                    }
                }
            });
            if((inputs[0].attr("type") === "radio" || inputs[0].is("select")) && maxPoints > 0){
                possiblePoints += maxPoints;
            }
        });

        var percentage = possiblePoints > 0 ? ((selectedPoints / possiblePoints) * 100).toFixed(2) : "0.00";
        updateStepUI($fieldset, selectedPoints, possiblePoints, percentage);

        // Save scores
        var index = $("fieldset").index($fieldset);
        var savedScores = JSON.parse(localStorage.getItem(scoreKey)) || {};
        savedScores[index] = { selectedPoints, possiblePoints, percentage };
        localStorage.setItem(scoreKey, JSON.stringify(savedScores));
    }

});
