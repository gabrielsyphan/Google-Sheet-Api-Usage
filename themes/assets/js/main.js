AOS.init({
    duration: 800,
    easing: 'slide',
    once: true
});

jQuery(document).ready(function ($) {

    "use strict";


    $(".loader").delay(1000).fadeOut("slow");
    $("#overlayer").delay(1000).fadeOut("slow");


    let siteMenuClone = function () {

        $('.js-clone-nav').each(function () {
            let $this = $(this);
            $this.clone().attr('class', 'site-nav-wrap').appendTo('.site-mobile-menu-body');
        });


        setTimeout(function () {

            let counter = 0;
            $('.site-mobile-menu .has-children').each(function () {
                let $this = $(this);

                $this.prepend('<span class="arrow-collapse collapsed">');

                $this.find('.arrow-collapse').attr({
                    'data-toggle': 'collapse',
                    'data-target': '#collapseItem' + counter,
                });

                $this.find('> ul').attr({
                    'class': 'collapse',
                    'id': 'collapseItem' + counter,
                });

                counter++;

            });

        }, 1000);

        $(window).resize(function () {
            let $this = $(this),
                w = $this.width();

            if (w > 768) {
                if ($('body').hasClass('offcanvas-menu')) {
                    $('body').removeClass('offcanvas-menu');
                }
            }
        })

        $('body').on('click', '.js-menu-toggle', function (e) {
            let $this = $(this);
            e.preventDefault();

            if ($('body').hasClass('offcanvas-menu')) {
                $('body').removeClass('offcanvas-menu');
                $this.removeClass('active');
            } else {
                $('body').addClass('offcanvas-menu');
                $this.addClass('active');
            }
        })

        // click outisde offcanvas
        $(document).mouseup(function (e) {
            let container = $(".site-mobile-menu");
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                if ($('body').hasClass('offcanvas-menu')) {
                    $('body').removeClass('offcanvas-menu');
                }
            }
        });
    };
    siteMenuClone();


    let sitePlusMinus = function () {
        $('.js-btn-minus').on('click', function (e) {
            e.preventDefault();
            if ($(this).closest('.input-group').find('.form-control').val() != 0) {
                $(this).closest('.input-group').find('.form-control').val(parseInt($(this).closest('.input-group').find('.form-control').val()) - 1);
            } else {
                $(this).closest('.input-group').find('.form-control').val(parseInt(0));
            }
        });
        $('.js-btn-plus').on('click', function (e) {
            e.preventDefault();
            $(this).closest('.input-group').find('.form-control').val(parseInt($(this).closest('.input-group').find('.form-control').val()) + 1);
        });
    };
    // sitePlusMinus();


    let siteSliderRange = function () {
        $("#slider-range").slider({
            range: true,
            min: 0,
            max: 500,
            values: [75, 300],
            slide: function (event, ui) {
                $("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
            }
        });
        $("#amount").val("$" + $("#slider-range").slider("values", 0) +
            " - $" + $("#slider-range").slider("values", 1));
    };
    // siteSliderRange();


    let siteCarousel = function () {
        if ($('.nonloop-block-13').length > 0) {
            $('.nonloop-block-13').owlCarousel({
                center: false,
                items: 1,
                loop: true,
                stagePadding: 0,
                margin: 0,
                autoplay: true,
                nav: true,
                navText: ['<span class="icon-arrow_back">', '<span class="icon-arrow_forward">'],
                responsive: {
                    600: {
                        margin: 0,
                        nav: true,
                        items: 2
                    },
                    1000: {
                        margin: 0,
                        stagePadding: 0,
                        nav: true,
                        items: 3
                    },
                    1200: {
                        margin: 0,
                        stagePadding: 0,
                        nav: true,
                        items: 4
                    }
                }
            });
        }

        $('.single-text').owlCarousel({
            center: false,
            items: 1,
            loop: true,
            stagePadding: 0,
            margin: 0,
            autoplay: true,
            pauseOnHover: false,
            nav: false,
            smartSpeed: 1000,
        });
        $('.slide-one-item').owlCarousel({
            center: false,
            items: 1,
            loop: true,
            stagePadding: 0,
            margin: 0,
            autoplay: true,
            smartSpeed: 1000,
            pauseOnHover: false,
            nav: true,
            navText: ['<span class="icon-keyboard_arrow_left">', '<span class="icon-keyboard_arrow_right">']
        });


        $('.slide-one-item-alt').owlCarousel({
            center: false,
            items: 1,
            loop: true,
            stagePadding: 0,
            margin: 0,
            smartSpeed: 1000,
            autoplay: true,
            pauseOnHover: true,
            mouseDrag: false,
            touchDrag: false,
        });
        $('.slide-one-item-alt-text').owlCarousel({
            center: false,
            items: 1,
            loop: true,
            stagePadding: 0,
            margin: 0,
            smartSpeed: 1000,
            autoplay: true,
            pauseOnHover: true,
            mouseDrag: false,
            touchDrag: false,

        });


        $('.custom-next').click(function (e) {
            e.preventDefault();
            $('.slide-one-item-alt').trigger('next.owl.carousel');
            $('.slide-one-item-alt-text').trigger('next.owl.carousel');
        });
        $('.custom-prev').click(function (e) {
            e.preventDefault();
            $('.slide-one-item-alt').trigger('prev.owl.carousel');
            $('.slide-one-item-alt-text').trigger('prev.owl.carousel');
        });

    };
    siteCarousel();

    let siteStellar = function () {
        $(window).stellar({
            responsive: false,
            parallaxBackgrounds: true,
            parallaxElements: true,
            horizontalScrolling: false,
            hideDistantElements: false,
            scrollProperty: 'scroll'
        });
    };
    // siteStellar();

    let siteCountDown = function () {

        $('#date-countdown').countdown('2020/10/10', function (event) {
            let $this = $(this).html(event.strftime(''
                + '<span class="countdown-block"><span class="label">%w</span> weeks </span>'
                + '<span class="countdown-block"><span class="label">%d</span> days </span>'
                + '<span class="countdown-block"><span class="label">%H</span> hr </span>'
                + '<span class="countdown-block"><span class="label">%M</span> min </span>'
                + '<span class="countdown-block"><span class="label">%S</span> sec</span>'));
        });

    };
    siteCountDown();

    let siteDatePicker = function () {

        if ($('.datepicker').length > 0) {
            $('.datepicker').datepicker();
        }

    };
    siteDatePicker();

    let siteSticky = function () {
        $(".js-sticky-header").sticky({topSpacing: 0});
    };
    siteSticky();


    let siteScroll = function () {

        $(window).scroll(function () {
            let st = $(this).scrollTop();

            if (st > 100) {
                $('.js-sticky-header').addClass('shrink');
            } else {
                $('.js-sticky-header').removeClass('shrink');
            }
        })
    };
    siteScroll();


    let siteIstotope = function () {
        let $container = $('#posts').isotope({
            itemSelector: '.item',
            isFitWidth: true
        });

        $(window).resize(function () {
            $container.isotope({
                columnWidth: '.col-sm-3'
            });
        });

        $container.isotope({filter: '*'});

        $('#filters').on('click', 'button', function () {
            let filterValue = $(this).attr('data-filter');
            $container.isotope({filter: filterValue});
            $('#filters button').removeClass('active');
            $(this).addClass('active');
        });
    }

    siteIstotope();


    $('.fancybox').on('click', function () {
        let visibleLinks = $('.fancybox');

        $.fancybox.open(visibleLinks, {}, visibleLinks.index(this));

        return false;
    });
});

function checkCpf(e) {
    let cpf = e;

    let sum;
    let aux;
    sum = 0;

    if (cpf == "00000000000") {
        return false;
    }
    for (i = 1; i <= 9; i++) sum = sum + parseInt(cpf.substring(i - 1, i)) * (11 - i);
    aux = (sum * 10) % 11;

    if ((aux == 10) || (aux == 11)) {
        aux = 0;
    }
    if (aux != parseInt(cpf.substring(9, 10))) {
        return false;
    }

    sum = 0;
    for (i = 1; i <= 10; i++) {
        sum = sum + parseInt(cpf.substring(i - 1, i)) * (12 - i);
        aux = (sum * 10) % 11;
    }

    if ((aux == 10) || (aux == 11)) {
        aux = 0;
    }

    if (aux != parseInt(cpf.substring(10, 11))) {
        return false;
    }
    return true;
}

function formatedCPF(e) {
    let cpf = $('#' + e.id).val();
    cpf = cpf.split('-');
    cpf = cpf[0] + '' + cpf[1];
    cpf = cpf.split('.');
    cpf = cpf[0] + '' + cpf[1] + '' + cpf[2];
    return cpf;
}

function nextPage(current, next) {
    $("#page-" + current).hide();
    $(".box-" + current).removeClass("active");
    $("#img-page-" + current).hide();
    $("#page-" + next).show();
    $(".box-" + next).addClass("active");
    $("#img-page-" + next).show();

    if (next == 5) {
        setTimeout(function () {
            map.invalidateSize();
        }, 100);
    }
}

function prevPage(current, prev) {
    $("#page-" + current).hide();
    $(".box-" + current).removeClass("active");
    $("#img-page-" + current).hide();
    $("#page-" + prev).show();
    $(".box-" + prev).addClass("active");
    $("#img-page-" + prev).show();

    if (prev == 5) {
        setTimeout(function () {
            map.invalidateSize();
        }, 100);
    }
}

function licenseChange(licenseOption) {
    for (let i = 0; i <= 5; i++) {
        $(".license-" + i).hide('fast');
    }
    $(".license-" + licenseOption).css('display', 'flex');
}

function uploadImage(e) {
    const file = $('#' + e.id).prop('files')[0];
    let fileName = file.name;
    let ext = fileName.substr(fileName.lastIndexOf('.') + 1);
    if (ext === 'jpg' || ext === 'jpeg' || ext === 'png' || ext === 'JPG' || ext === 'JPEG' || ext === 'PNG') {
        if (file.size > 1133695) {
            swal({
                icon: "warning",
                title: "Atenção",
                text: "Por favor, insira uma imagem com no máximo 1mb de tamanho.",
            });
            $('#' + e.id).val('');
        } else {
            $('.' + e.id + '-file').hide();
            $('.' + e.id + '-file-uploaded').css('display', 'flex');
            $('.' + e.id + '-name').append(fileName);
            $('.' + e.id + '-type').append(ext);
        }
    } else {
        swal({
            icon: "error",
            title: "Erro",
            text: "O tipo do anexo é inválido. Por favor, insira uma imagem em formato JPEG, JPG ou PNG.",
        });
        $('#' + e.id).val('');
    }
}

function formSubmit(form) {
    const _thisForm = $('#' + form.id);
    _thisForm.find(".is-invalid").removeClass("is-invalid").next().text("");
    let validate = true;

    _thisForm.find(':input').not(':button, [name="productDescription"], [name="description"], [name="zoneImage"], ' +
        '[name="penality"], [type="hidden"], [id="agentImage"]').each(function () {
        if (!$(this).val()) {
            $(this).addClass('is-invalid').next().text('Campo obrigatório!');
            validate = false;
        } else if ($(this).attr('name') == 'name') {
            let name = $('#' + $(this).attr('id')).val();
            name = name.split(' ');
            if (!name[1]) {
                $("#" + $(this).attr('id')).addClass('is-invalid').next().text('Insira seu nome completo!');
                validate = false;
            }
        }
    });

    if (validate == false) {
        const fieldsetDisable = _thisForm.find('fieldset');
        fieldsetDisable.removeAttr("disabled");
        $("#loader-div").hide();
    }

    return validate;
}

function checkCpf(e) {
    let cpf = e;

    let sum;
    let aux;
    sum = 0;

    if (cpf == "00000000000") {
        return false;
    }
    for (i = 1; i <= 9; i++) sum = sum + parseInt(cpf.substring(i - 1, i)) * (11 - i);
    aux = (sum * 10) % 11;

    if ((aux == 10) || (aux == 11)) {
        aux = 0;
    }
    if (aux != parseInt(cpf.substring(9, 10))) {
        return false;
    }

    sum = 0;
    for (i = 1; i <= 10; i++) {
        sum = sum + parseInt(cpf.substring(i - 1, i)) * (12 - i);
        aux = (sum * 10) % 11;
    }

    if ((aux == 10) || (aux == 11)) {
        aux = 0;
    }

    if (aux != parseInt(cpf.substring(10, 11))) {
        return false;
    }
    return true;
}

function formatedCPF(e) {
    let cpf = $('#' + e.id).val();
    cpf = cpf.split('-');
    cpf = cpf[0] + '' + cpf[1];
    cpf = cpf.split('.');
    cpf = cpf[0] + '' + cpf[1] + '' + cpf[2];
    return cpf;
}

function nextPage(current, next) {
    $("#page-" + current).hide();
    $(".box-" + current).removeClass("active");
    $("#img-page-" + current).hide();
    $("#page-" + next).show();
    $(".box-" + next).addClass("active");
    $("#img-page-" + next).show();

    if (next == 2) {
        setTimeout(function () {
            map.invalidateSize();
        }, 100);
    }
}

function prevPage(current, prev) {
    $("#page-" + current).hide();
    $(".box-" + current).removeClass("active");
    $("#img-page-" + current).hide();
    $("#page-" + prev).show();
    $(".box-" + prev).addClass("active");
    $("#img-page-" + prev).show();

    if (prev == 2) {
        setTimeout(function () {
            map.invalidateSize();
        }, 100);
    }
}

function licenseChange(licenseOption) {
    for (let i = 0; i <= 5; i++) {
        $(".license-" + i).hide('fast');
    }
    $(".license-" + licenseOption).css('display', 'flex');
}

function uploadImage(e) {
    const file = $('#' + e.id).prop('files')[0];
    let fileName = file.name;
    let ext = fileName.substr(fileName.lastIndexOf('.') + 1);
    if (ext === 'jpg' || ext === 'jpeg' || ext === 'png' || ext === 'JPG' || ext === 'JPEG' || ext === 'PNG') {
        if (file.size > 1133695) {
            swal({
                icon: "warning",
                title: "Atenção",
                text: "Por favor, insira uma imagem com no máximo 1mb de tamanho.",
            });
            $('#' + e.id).val('');
        } else {
            $('.' + e.id + '-file').hide();
            $('.' + e.id + '-file-uploaded').css('display', 'flex');
            $('.' + e.id + '-name').append(fileName);
            $('.' + e.id + '-type').append(ext);
        }
    } else {
        swal({
            icon: "error",
            title: "Erro",
            text: "O tipo do anexo é inválido. Por favor, insira uma imagem em formato JPEG, JPG ou PNG.",
        });
        $('#' + inputName).val('');
    }
}

function changeFile(e) {
    let inputId = e.id;
    inputId = inputId.split('-')[0];
    $('.' + inputId + '-file-uploaded').hide();
    $('.hidden-input-file').val(null);
    $('.' + inputId + '-file').show();
    $('.' + inputId + '-type').empty();
    $('.' + inputId + '-name').empty();
}

function openModal(e) {
    $('#modal-' + e).show();
}

function closeModal(e) {
    $('#modal-' + e).hide();
}

function openFile(url) {
    window.open('https://localhost/orditi/themes/assets/uploads/' + url, '_blank');
}
