<!DOCTYPE html>

<html dir="{{ session('locale') == 'ar' ? 'rtl' : 'ltr' }}" lang="{{ session('locale') == 'ar' ? 'ar' : 'en' }}"
    style="text-align: {{ session('locale') == 'ar' ? 'right' : 'left' }};">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Title -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>
    <meta name="_token" content="{{ csrf_token() }}">
    <!--to make http ajax request to https-->
    <!--    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">-->

    <!-- Favicon -->
    <?php $web_config['fav_icon'] = 'Eslam Badawy'; ?>
    <link rel="shortcut icon" href="{{ asset('assets/finexerp_logo.png') }}">

    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap"
        rel="stylesheet">
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{ asset('assets/back-end/css/vendor.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/back-end/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/back-end/css/custom.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-d...">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('assets/back-end/vendor/icon-set/style.css') }}">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{ asset('assets/back-end/css/theme.minc619.css?v=1.0') }}">
    <link rel="stylesheet" href="{{ asset('assets/back-end/css/style.css') }}">
    @if (Session::get('locale') === 'ar')
        <link rel="stylesheet" href="{{ asset('assets/back-end/css/menurtl.css') }}">
    @endif

    {{-- light box --}}
    {{-- <link rel="stylesheet" href="{{ asset('public/css/lightbox.css') }}"> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    @stack('css_or_js')

    <style>
        select {
            background-image: url('{{ asset('/assets/back-end/img/arrow-down.png') }}');
            background-size: 7px;
            background-position: 96% center;
        }

        .starColor {
            color: red
        }

        .navbar-vertical-content::-webkit-scrollbar {
            width: 12px !important;
            height: 7px !important;
        }
    </style>
    <script src="{{ asset('assets/back-end/vendor/hs-navbar-vertical-aside/hs-navbar-vertical-aside-mini-cache.js') }}">
    </script>
    <link rel="stylesheet" href="{{ asset('assets/back-end/css/toastr.css') }}">
</head>

<body class="footer-offset">
    @php
            $company = (new App\Models\Company())->setConnection('tenant')->select('id' , 'logo_image')->first();

    @endphp
    <!-- Builder -->
    @include('layouts.back-end.partials._front-settings')
    <!-- End Builder -->
    {{-- loader --}}
    {{-- <div class="row">
        <div class="col-12 position-fixed z-9999 mt-10rem">
            <div id="loading" style="display: none">
                <div id="loader"></div>
            </div>
        </div>
    </div> --}}
    {{-- loader --}}

    <!-- JS Preview mode only -->
    @include('layouts.back-end.partials._header')
    @include('layouts.back-end.partials._side-bar')

    <!-- END ONLY DEV -->

    <main id="content" role="main" class="main pointer-event">
        <!-- Content -->
        @yield('content')
        <!-- End Content -->

        <!-- Footer -->
        @include('layouts.back-end.partials._footer')
        <!-- End Footer -->

        @include('layouts.back-end.partials._modals')

        {{-- Toggle Modal --}}
        @include('layouts.back-end.partials._toggle-modal')
    </main>
    <!-- ========== END MAIN CONTENT ========== -->
    @if (Session::has('success'))
        <script>
            swal("Message", "{{ Session::get('success') }}", 'success', {
                button: true,
                button: "Ok",
                timer: 3000,
            })
        </script>
    @endif
    @if (Session::has('error'))
        <script>
            swal("Message", "{{ Session::get('error') }}", 'error', {
                button: true,
                button: "Ok",
                timer: 3000,
            })
        </script>
    @endif
    {{-- @if (session('success'))
        <script>
            toastr.success("{{ session('success') }}");
        </script>
    @endif

    @if (session('error'))
        <script>
            toastr.error("{{ session('error') }}");
        </script>
    @endif --}}

    <span class="please_fill_out_this_field" data-text="{{ __('please_fill_out_this_field') }}"></span>

    {{-- <span class="please_fill_out_this_field" data-text="{{ __('please_fill_out_this_field') }}"></span> --}}

    <!-- ========== END SECONDARY CONTENTS ========== -->
    <script src="{{ asset('assets/back-end') }}/js/custom.js"></script>
    <!-- JS Implementing Plugins -->

    {{-- @stack('script') --}}

    <!-- JS Front -->
    {{-- <script src="{{ asset('js/jquery/jquery.min.js') }}"></script> --}}
    <script src="{{ asset('assets/back-end') }}/js/vendor.min.js"></script>
    <script src="{{ asset('assets/back-end') }}/js/theme.min.js"></script>
    <script src="{{ asset('assets/back-end') }}/js/sweet_alert.js"></script>
    <script src="{{ asset('assets/back-end') }}/js/toastr.js"></script>
    <script>
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-bottom-left",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    </script>

    @if ($errors->any())
        <script>
            @foreach ($errors->all() as $error)
                toastr.error('{{ $error }}', Error, {
                    CloseButton: true,
                    ProgressBar: true
                });
            @endforeach
        </script>
    @endif
    <!-- JS Plugins Init. -->
    <script>
        $(document).on('ready', function() {
            "use strict"

            // Loading Off
            // $('#loading').fadeOut();

            // Convert SVG code
            // =======================================================
            $("img.svg").each(function() {
                var $img = jQuery(this);
                var imgID = $img.attr("id");
                var imgClass = $img.attr("class");
                var imgURL = $img.attr("src");

                jQuery.get(
                    imgURL,
                    function(data) {
                        // Get the SVG tag, ignore the rest
                        var $svg = jQuery(data).find("svg");

                        // Add replaced image's ID to the new SVG
                        if (typeof imgID !== "undefined") {
                            $svg = $svg.attr("id", imgID);
                        }
                        // Add replaced image's classes to the new SVG
                        if (typeof imgClass !== "undefined") {
                            $svg = $svg.attr("class", imgClass + " replaced-svg");
                        }

                        // Remove any invalid XML tags as per http://validator.w3.org
                        $svg = $svg.removeAttr("xmlns:a");

                        // Check if the viewport is set, else we gonna set it if we can.
                        if (
                            !$svg.attr("viewBox") &&
                            $svg.attr("height") &&
                            $svg.attr("width")
                        ) {
                            $svg.attr(
                                "viewBox",
                                "0 0 " + $svg.attr("height") + " " + $svg.attr("width")
                            );
                        }

                        // Replace image with new SVG
                        $img.replaceWith($svg);
                    },
                    "xml"
                );
            });


            // ONLY DEV
            // =======================================================
            if (window.localStorage.getItem('hs-builder-popover') === null) {
                $('#builderPopover').popover('show')
                    .on('shown.bs.popover', function() {
                        $('.popover').last().addClass('popover-dark')
                    });

                $(document).on('click', '#closeBuilderPopover', function() {
                    window.localStorage.setItem('hs-builder-popover', true);
                    $('#builderPopover').popover('dispose');
                });
            } else {
                $('#builderPopover').on('show.bs.popover', function() {
                    return false
                });
            }
            // END ONLY DEV
            // =======================================================

            // BUILDER TOGGLE INVOKER
            // =======================================================
            $('.js-navbar-vertical-aside-toggle-invoker').click(function() {
                $('.js-navbar-vertical-aside-toggle-invoker i').tooltip('hide');
            });

            // INITIALIZATION OF MEGA MENU
            // =======================================================
            /*var megaMenu = new HSMegaMenu($('.js-mega-menu'), {
                desktop: {
                    position: 'left'
                }
            }).init();*/


            // INITIALIZATION OF NAVBAR VERTICAL NAVIGATION
            // =======================================================
            var sidebar = $('.js-navbar-vertical-aside').hsSideNav();


            // INITIALIZATION OF TOOLTIP IN NAVBAR VERTICAL MENU
            // =======================================================
            $('.js-nav-tooltip-link').tooltip({
                boundary: 'window'
            })

            $(".js-nav-tooltip-link").on("show.bs.tooltip", function(e) {
                if (!$("body").hasClass("navbar-vertical-aside-mini-mode")) {
                    return false;
                }
            });


            // INITIALIZATION OF UNFOLD
            // =======================================================
            $('.js-hs-unfold-invoker').each(function() {
                var unfold = new HSUnfold($(this)).init();
            });


            // INITIALIZATION OF FORM SEARCH
            // =======================================================
            $('.js-form-search').each(function() {
                new HSFormSearch($(this)).init()
            });


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function() {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });


            // INITIALIZATION OF DATERANGEPICKER
            // =======================================================
            $('.js-daterangepicker').daterangepicker();

            $('.js-daterangepicker-times').daterangepicker({
                timePicker: true,
                startDate: moment().startOf('hour'),
                endDate: moment().startOf('hour').add(32, 'hour'),
                locale: {
                    format: 'M/DD hh:mm A'
                }
            });

            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#js-daterangepicker-predefined .js-daterangepicker-predefined-preview').html(start.format(
                    'MMM D') + ' - ' + end.format('MMM D, YYYY'));
            }

            $('#js-daterangepicker-predefined').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            }, cb);

            cb(start, end);


            // INITIALIZATION OF CLIPBOARD
            // =======================================================
            $('.js-clipboard').each(function() {
                var clipboard = $.HSCore.components.HSClipboard.init(this);
            });
        });
    </script>
    <script>
        $("#reset").on('click', function() {
            let placeholderImg = $("#placeholderImg").data('img');
            $('#viewer').attr('src', placeholderImg);
            $('.spartan_remove_row').click();
        });

        function openInfoWeb() {
            var x = document.getElementById("website_info");
            if (x.style.display === "none") {
                x.style.display = "block";
            } else {
                x.style.display = "none";
            }
        }
    </script>


    <script src="{{ asset('public/assets/back-end') }}/js/bootstrap.min.js"></script>
    {{-- light box --}}
    <script src="{{ asset('public/js/lightbox.min.js') }}"></script>
    <audio id="myAudio">
        <source src="{{ asset('public/assets/back-end/sound/notification.mp3') }}" type="audio/mpeg">
    </audio>
    <script>
        var audio = document.getElementById("myAudio");

        function playAudio() {
            audio.play();
        }

        function pauseAudio() {
            audio.pause();
        }
    </script>
    <script>
        setInterval(function() {
            $.get({
                url: '',
                dataType: 'json',
                success: function(response) {
                    let data = response.data;
                    if (data.new_order > 0) {
                        playAudio();
                        $('#popup-modal').appendTo("body").modal('show');
                    }
                },
            });
        }, 10000);


        function check_order() {
            location.href = '/';
        }
    </script>
    <script>
        $(document).mouseup(function(e) {
            var container = $("#search-card");
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                container.hide();
            }
        });

        function form_alert(id, message) {
            Swal.fire({
                title: '{{ __('are_you_sure') }}?',
                text: message,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: '{{ __('no') }}',
                confirmButtonText: '{{ __('yes') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#' + id).submit()
                }
            })
        }
    </script>

    <script>
        function call_demo() {
            toastr.info('{{ __('update_option_is_disabled_for_demo') }}!', {
                CloseButton: true,
                ProgressBar: true
            });
        }
    </script>

    <!-- IE Support -->
    <script>
        if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write(
            '<script src="{{ asset('public/assets/back-end') }}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
    </script>
    @stack('script_2')


    <script>
        $(document).ready(function() {
            $("#toggle-sidebar").click(function() {
                $(".navbar-vertical-aside").toggleClass("collapsed");
                $(".navbar-vertical-aside-toggle-short-align, .navbar-vertical-aside-toggle-full-align")
                    .toggleClass("d-none");
            });
        });
    </script>
    <!-- <script>
        initSample();
    </script> -->

    <script>
        function toogleModal(e, toggle_id, on_image, off_image, on_title, off_title, on_message, off_message) {
            e.preventDefault();
            if ($('#' + toggle_id).is(':checked')) {
                $('#toggle-title').empty().append(on_title);
                $('#toggle-message').empty().append(on_message);
                $('#toggle-image').attr('src', "{{ asset('/public/assets/back-end/img/modal') }}/" + on_image);
                $('#toggle-ok-button').attr('toggle-ok-button', toggle_id);
            } else {
                $('#toggle-title').empty().append(off_title);
                $('#toggle-message').empty().append(off_message);
                $('#toggle-image').attr('src', "{{ asset('/public/assets/back-end/img/modal') }}/" + off_image);
                $('#toggle-ok-button').attr('toggle-ok-button', toggle_id);
            }
            $('#toggle-modal').modal('show');
        }

        function confirmToggle() {
            var toggle_id = $('#toggle-ok-button').attr('toggle-ok-button');
            if ($('#' + toggle_id).is(':checked')) {
                $('#' + toggle_id).prop('checked', false);
            } else {
                $('#' + toggle_id).prop('checked', true);
            }
            $('#toggle-modal').modal('hide');

            if (toggle_id == 'email_verification') {
                if ($("#email_verification").is(':checked')) {
                    $('#otp_verification').removeAttr('checked');
                }
            }

            if (toggle_id == 'otp_verification') {
                if ($("#otp_verification").is(':checked')) {
                    $('#email_verification').removeAttr('checked');
                }
            }
        }

        function toogleStatusModal(e, toggle_id, on_image, off_image, on_title, off_title, on_message, off_message) {
            e.preventDefault();
            $('.toggle-modal-img-box .status-icon').attr('src', '');
            if ($('#' + toggle_id).is(':checked')) {
                $('#toggle-status-title').empty().append(on_title);
                $('#toggle-status-message').empty().append(on_message);
                $('#toggle-status-image').attr('src', "{{ asset('/public/assets/back-end/img/modal') }}/" + on_image);
                $('#toggle-status-ok-button').attr('toggle-ok-button', toggle_id);
            } else {
                $('#toggle-status-title').empty().append(off_title);
                $('#toggle-status-message').empty().append(off_message);
                $('#toggle-status-image').attr('src', "{{ asset('/public/assets/back-end/img/modal') }}/" + off_image);
                $('#toggle-status-ok-button').attr('toggle-ok-button', toggle_id);
            }
            $('#toggle-status-modal').modal('show');
        }

        function confirmStatusToggle() {
            var toggle_id = $('#toggle-status-ok-button').attr('toggle-ok-button');
            if ($('#' + toggle_id).is(':checked')) {
                $('#' + toggle_id).prop('checked', false);
                $('#' + toggle_id).val(0);
            } else {
                $('#' + toggle_id).prop('checked', true);
                $('#' + toggle_id).val(1);
            }
            $('#' + toggle_id + '_form').submit();
        }
    </script>
    <script>
        function getRndInteger() {
            return Math.floor(Math.random() * 90000) + 100000;
        }

        // Input Field Validation Custom Message
        var errorMessages = {
            valueMissing: $('.please_fill_out_this_field').data('text')
        };

        $('input').each(function() {
            var $el = $(this);

            $el.on('invalid', function(event) {
                var target = event.target,
                    validity = target.validity;
                target.setCustomValidity("");
                if (!validity.valid) {
                    if (validity.valueMissing) {
                        target.setCustomValidity($el.data('errorRequired') || errorMessages.valueMissing);
                    }
                }
            });
        });
    </script>

    <script>
        $(document).on('change', '.bulk_check_all', function() {
            $('input.check_bulk_item:checkbox').not(this).prop('checked', this.checked);
        });
    </script>

    @stack('script')
</body>

</html>
