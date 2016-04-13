"use strict";
jQuery(document).ready(function($) {
    /****************Je r�cupere les donn�es data****************/
    var cat = $("#filter-init").data("cat-slug");
    var count = 2;
    var total = $("#filter-init").data("number-page");
    var search = jQuery('.page-header-search').data('search');
    var tag = jQuery('.page-header-data').data('tag');
    var author = jQuery('.page-header-data').data('author');
    var day = jQuery('#filter-init').data('day');
    var month = jQuery('#filter-init').data('month');
    var year = jQuery('#filter-init').data('year');

    function zellira_filter(pageNumber, typeFilter, el) {
        /***********Si l'utilisateur utilise la bar de filtre***********/
        if(typeFilter == "filter"){
            $("html, body").animate({
                scrollTop: 0
            }, 800);
            /****************Je récupere le parent du type de filtre(orderby, date,...) utilisé et séléctionne tout ses enfants*************/
            var parent = $(el).closest('ul').find('li a');
            var ela = $(el).find('a');
            /*J'efface les posts*/
            $('.main article').removeClass('animated fadeIn');
            $('.main article').addClass('animated fadeOut');
            parent.removeClass('active');
            /*********le lien cliqu� devient actif**************/
            $(ela).addClass("active");
            /*********************Si le filtre order-by ASC/DESC et sticky est utilisé je cache celui-ci et affiche son opposé *****************************/
            if ($(el).closest('ul').hasClass('order-by') == true) {
                $(el).closest('ul').find('li a.active').css('display', 'none');
                $(parent).not('.active').css('display', 'inline-table');
            }
            /******Je remet le nombre de page au point de depart***********/
            count = 2;
        }
        /****Value data bar filter*/
        var data_sort = jQuery('.filter-order a.active').data('post-type');
        var type_post = jQuery('.filter-type a.active').data('post-type');
        var order_by = jQuery('.filter-sort-by a.active').data('post-type');
        var data_sticky = jQuery('.filter-sticky a.active').data('post-type');
        if (type_post == 'post') {
            type_post = null;
        }
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                filter_type : typeFilter,
                action: 'zellira_filter',
                order: data_sort,
                orderby: order_by,
                post_format: type_post,
                page_no: pageNumber,
                cat: cat,
                search_query: search,
                tag: tag,
                year: year,
                month: month,
                day  : day,
                author : author,
                sticky_post: data_sticky
            },
            xhrFields: {
                withCredentials: true
            },
            success: function (html) {
                if (html.length > 0) {
                    Pace.restart();
                var $container = $('.main.mason');
                var $newElems = jQuery(html);
                /************Si l'utilisateur utilise la bar de filtre*****************/
                if (typeFilter == "filter") {

                    if ($('#filter-control > p').hasClass('active')) {
                        $('.main').css('margin-top', '30px');
                    }
                    // Remove all blocks
                    var $lessBlocks = jQuery('.main .grid_post');
                    jQuery(".main.mason").masonry('remove', $lessBlocks);
                    jQuery(".main.mason").masonry();
                    //Unloaded images can throw off Masonry layouts and cause item elements to overlap. imagesLoaded resolves this issue.
                        jQuery(".main").append($newElems).imagesLoaded(function () {
                            // pour chaque new element je les affiches
                            $.each($newElems, function (i, el) {
                                jQuery(el).find('audio,video').mediaelementplayer();
                                jQuery(".main.mason").masonry('prepended', el, true);
                                jQuery(".main.mason").masonry('reload');
                                // je cree les gallery avec owl carousel
                                $(el).find(".gallery").owlCarousel(valueGlobal(true, false));
                                // j'affiche progresssivement les new posts
                                setTimeout(function () {
                                    $(el).css('visibility', 'visible');
                                    $(el).addClass('animated fadeIn');
                                }, 5 + ( i * 50));
                            });
                        });
                } else {
                    /********************/
                    // Append new blocks
                    //Unloaded images can throw off Masonry layouts and cause item elements to overlap. imagesLoaded resolves this issue.
                    $container.prepend($newElems).imagesLoaded(function () {
                        $.each($newElems, function (i, el) {
                            //J'ajoute a la suite les new posts
                            $container.append(el).masonry('reload');
                            $(el).find(".gallery").owlCarousel(valueGlobal(true, false));
                            jQuery(el).find('audio,video').mediaelementplayer();
                            setTimeout(function () {
                                $(el).css('visibility', 'visible');
                                $(el).addClass('animated fadeIn');
                            }, 5 + ( i * 50));
                        });
                    });
                }
            }
            },
            error: function (MLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
        if(typeFilter == "filter"){
            userFiltre($(ela));
        }
    }
// La function qui permet de remplire les filtres de l'utilisateurs
    function userFiltre(el) {
        var id_this = $(el).data('filter');
        var id_filter = jQuery('#filter-selected #' + id_this);
        id_filter.addClass('active');
        var value_filter = id_filter.find('span');
        value_filter.empty();
        value_filter.append($(el).text());
    }
// add/delete les informations de la barre de filtre enregistr� (function userfiltre())
    $('#filter-selected p').click(function () {
        var filter_id = $(this).attr('id');
        var filter_control = $('#filter-control .' + filter_id);
        filter_control.find('li a.active').removeClass('active');
        var post_type = filter_control.find('li a').data('post-type');
        filter_control.find('li a.' + post_type).addClass('active');
        $(this).removeClass("active");
        //Si userFilter() contient plus de class active je cache la bar userfiltre()
        var ac_filter = jQuery('#filter-selected p').hasClass('active');
        if(ac_filter == false){
            $('.spacer').removeClass('open-filter')
        }
        zellira_filter("", "filter");
    });
    $('.sort li').click(function () {
        zellira_filter("", "filter", this);
    });
    jQuery(window).scroll(function () {
        if (jQuery(window).scrollTop() == jQuery(document).height() - jQuery(window).height() ) {
            if (count > total) {
                return false;
            } else {
                zellira_filter(count, "scroll");
            }
            count++;
        }
    });
});
