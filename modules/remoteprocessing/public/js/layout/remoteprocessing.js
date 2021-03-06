// Midas Server. Copyright Kitware SAS. Licensed under the Apache License 2.0.

/* global json */

var midas = midas || {};

$(document).ready(function () {
    'use strict';
    var uploadPageLoaded = false;
    $('div.HeaderAction li.processButton').click(function () {
        $('.uploadqtip').css('z-index', '800');
        if (json.global.logged) {
            if (!uploadPageLoaded) {
                $('img#processButtonImg').hide();
                $('img#processButtonLoadiing').show();
                uploadPageLoaded = true;
            }
        } else {
            midas.createNotice(json.login.contentUploadLogin, 4000);
            $('div.TopDynamicBar').show('blind');
            midas.loadAjaxDynamicBar('login', '/user/login');
        }
    });

    if (json.global.logged) {
        $('div.HeaderAction li.processButton').qtip({
            content: {
                // Set the text to an image HTML string with the correct src URL to the loading image you want to use
                text: '<img  src="' + json.global.webroot + '/core/public/images/icons/loading.gif" alt="Loading..." />',
                ajax: {
                    url: $('div.HeaderAction li.processButton').attr('rel') // Use the rel attribute of each element for the url to load
                },
                title: {
                    text: 'Create a process', // Give the tooltip a title using each elements text
                    button: true
                }
            },
            position: {
                at: 'bottom center', // Position the tooltip above the link
                my: 'top right',
                viewport: $(window), // Keep the tooltip on-screen at all times
                effect: true // Disable positioning animation
            },
            show: {
                event: 'click',
                solo: true // Only show one tooltip at a time
            },
            hide: {
                event: false
            },
            style: {
                classes: 'uploadqtip ui-tooltip-light ui-tooltip-shadow ui-tooltip-rounded'
            }
        });
        $('.uploadqtip').css('z-index:500');
    }
});
