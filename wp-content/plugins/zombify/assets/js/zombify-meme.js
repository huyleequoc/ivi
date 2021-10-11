var zombify_canvas, zombify_settings, zombify_meme_img_delta;

jQuery(document).ready(function ($) {

    zombify_settings = {
        global: {
            width: 0
        },
        items: {
            1: {
                text: zf.translatable.meme.top_text,
                top: 25,
                left: 15,
                width: 500,
                height: 60,
                color: '#ffffff',
                fontSize: 50,
                fontType: 'auto',
                fontFamily: 'Impact',
                shadow: '#000000',
                lineWidth: 2
            },
            2: {
                text: zf.translatable.meme.bottom_text,
                top: 25,
                left: 15,
                width: 500,
                height: 60,
                color: '#ffffff',
                fontSize: 50,
                fontType: 'auto',
                fontFamily: 'Impact',
                shadow: '#000000',
                lineWidth: 2
            }
        }

    };

    // Zombify meme popup open / close
    $(document).on('click', '.zf-meme-popup-btn', function (e) {
        e.preventDefault();
        $('.zombify-meme-popup').addClass('zf-open');
    });

    $(document).on('click', '.zombify-meme-popup .zf-popup_close', function (e) {
        e.preventDefault();
        $('.zombify-meme-popup').removeClass('zf-open');
    });

    initMeme($);

});

function initMeme($) {

    if ($("#meme_settings").length > 0) {
        if ($("#meme_settings").val() != '') {

            zombify_settings = decodeObj($("#meme_settings").val());

        }
    }


    if ($('#zf-meme').length) {

        //Global Vars
        var img, delta, mmContainerWidth;

        mmContainerWidth = $('#zf-main').innerWidth();

        if ($('#zf-meme').hasClass('zf-create')) {
            var zf_isCreate = true;
            var globalRatio = 1;
            zombify_settings.global.width = mmContainerWidth;
            zombify_settings.items['1'].width = zombify_settings.global.width-30;
            zombify_settings.items['2'].width = zombify_settings.global.width-30;

            if (mmContainerWidth <= 700) {
                zombify_settings.items['1'].top = 10;
                zombify_settings.items['1'].left = 10;
                zombify_settings.items['1'].height = 30;
                zombify_settings.items['1'].fontSize = 20;

                zombify_settings.items['2'].top = 10;
                zombify_settings.items['2'].left = 10;
                zombify_settings.items['2'].height = 30;
                zombify_settings.items['2'].fontSize = 20;
            }
        } else {
            var zf_isCreate = false;
            var globalRatio = zombify_settings.global.width / mmContainerWidth;
            zombify_settings.global.width = mmContainerWidth;

            zombify_settings.items['1'].top = zombify_settings.items['1'].top / globalRatio;
            zombify_settings.items['1'].left = zombify_settings.items['1'].left / globalRatio;
            zombify_settings.items['1'].width = zombify_settings.items['1'].width / globalRatio;
            zombify_settings.items['1'].height = zombify_settings.items['1'].height / globalRatio;
            zombify_settings.items['1'].fontSize = zombify_settings.items['1'].fontSize / globalRatio;

            zombify_settings.items['2'].top = zombify_settings.items['2'].top / globalRatio;
            zombify_settings.items['2'].left = zombify_settings.items['2'].left / globalRatio;
            zombify_settings.items['2'].width = zombify_settings.items['2'].width / globalRatio;
            zombify_settings.items['2'].height = zombify_settings.items['2'].height / globalRatio;
            zombify_settings.items['2'].fontSize = zombify_settings.items['2'].fontSize / globalRatio;
        }

        if (zf_isCreate) {
            // Trigger the imageLoader function when a file has been selected
            $('#meme_image').on('change', function () {

                if( !ZombifyBuilder.validateField( jQuery(this) ) ) return false;

                if (this.files && this.files[0]) {

                    var mm_reader = new FileReader();

                    mm_reader.onload = function (e) {

                        memeImageUpload( e.target.result );

                    }

                    mm_reader.readAsDataURL(this.files[0]);
                }
            })

            $(document).on('click', '.zombify-meme-popup .single-image', function (e) {

                $('.zf-start.zf-open').removeClass('zf-open');

                memeImageUpload( $(this).data('url') );

                $('.meme-template').val( $(this).data('url') );

                $('.zombify-meme-popup').removeClass('zf-open');

            });

        } else {
            var img = new Image();
            /* Set 'anonymous' to the 'crossOrigin' attribute to allow cross-origin requests */
            img.setAttribute( 'crossOrigin', 'anonymous' );
            img.onload = function () {
                memeInit();
            }
            img.src = document.getElementById('zf-meme-img').src;
        }

        function memeImageUpload(src) {
            var zf_img = new Image();

            $("#zf-meme-img").attr('src', src).show();
            $("#zf-memecontainer").show();
            $(".zf-media-uploader").hide();
            // Focus main Title field after page ready
            $('#zombify-main-section input[data-zombify-field-path="title"]:first').focus();

            $('html, body').animate({
                scrollTop: $('#zf-meme-img').offset().top - 20
            }, 'slow');
            //  Grab the image
            img = document.getElementById('zf-meme-img');
            // When the image has loaded...
            img.onload = function () {

                memeInit();
            }

            zf_img.onload = function() {
                zombify_meme_img_delta = zf_img.width / mmContainerWidth;
            }

            zf_img.src = src;
        }


        function memeInit() {

            var imgWidth = img.width,
                imgHeight = img.height;
            delta = imgWidth / imgHeight;

            if (!zf_isMobile) {
                $('.zf-options').css('max-width', mmContainerWidth - 30 + 'px');
                $(".zf-drag-area").draggable({
                    containment: "#zf-memecontainer",
                    handle: ".zf-drag",
                    create: function (event, ui) {
                        if (zf_isCreate) {
                            $(".zf-drag-area[data-rel='1']").css({
                                'top': zombify_settings.items['1'].top,
                                'left': zombify_settings.items['1'].left,
                                'width': zombify_settings.items['1'].width + 4 +'px' ,
                                'height': zombify_settings.items['1'].height + 4 +'px'
                            });
                            $(".zf-drag-area[data-rel='2']").css({
                                'top': (mmContainerWidth / delta) - zombify_settings.items['2'].height - 25,
                                'left': zombify_settings.items['2'].left,
                                'width': zombify_settings.items['2'].width + 4 +'px',
                                'height': zombify_settings.items['2'].height + 4 +'px'
                            });
                        } else {
                            $(".zf-drag-area[data-rel='1']").css({
                                'top': zombify_settings.items['1'].top,
                                'left': zombify_settings.items['1'].left,
                                'width': zombify_settings.items['1'].width,
                                'height': zombify_settings.items['1'].height
                            });
                            $(".zf-drag-area[data-rel='2']").css({
                                'top': zombify_settings.items['2'].top,
                                'left': zombify_settings.items['2'].left,
                                'width': zombify_settings.items['2'].width,
                                'height': zombify_settings.items['2'].height
                            });
                        }
                    },
                    drag: function (event, ui) {
                        zombify_settings.items[$(this).data('rel')].top = ui.position.top;
                        zombify_settings.items[$(this).data('rel')].left = ui.position.left;
                        zombify_settings.items[$(this).data('rel')].height = $(this).height();
                        zombify_settings.items[$(this).data('rel')].width = $(this).width();
                        zf_draw();
                    }
                }).resizable({
                    handles: "n, s, sw, nw, ne, se",
                    resize: function (event, ui) {
                        zombify_settings.items[$(this).data('rel')].top = ui.position.top;
                        zombify_settings.items[$(this).data('rel')].left = ui.position.left;
                        zombify_settings.items[$(this).data('rel')].height = $(this).height();
                        zombify_settings.items[$(this).data('rel')].width = $(this).width();
                        zf_draw();
                    }
                }).on('resize', function (e) {
                    e.stopPropagation();
                });
            }

            if (zf_isCreate) {
                zombify_settings.items['2'].top = (mmContainerWidth / delta) - zombify_settings.items['2'].height - 25;
                // zombify_settings.items['1'].width = mmContainerWidth - 30;
                // zombify_settings.items['2'].width = mmContainerWidth - 30;

                zf_draw(); //this fix long default text issue (when it calls second time below, text font & line count is correct)
            }
            zf_draw();
        }

        $('textarea.zf-write').keyup(function () {
            zombify_settings.items[$(this).data('rel')].text = $(this).val();
            zf_draw();
        });

        $('.zf-opt_top').keyup(function () {
            zombify_settings.items[$(this).data('rel')].top = parseInt($(this).val());
            zf_draw();
        });

        $('.zf-opt_left').keyup(function () {
            zombify_settings.items[$(this).data('rel')].left = parseInt($(this).val());
            zf_draw();
        });

        $('.zf-opt_fontSize').on('change', function () {
            if ($(this).val() == 'auto') {
                zombify_settings.items[$(this).data('rel')].fontType = 'auto';
            } else {
                zombify_settings.items[$(this).data('rel')].fontType = $(this).val();
                zombify_settings.items[$(this).data('rel')].fontSize = $(this).val();
            }
            zf_draw();
        });

        $('.zf-opt_fontFamily').on('change', function () {
            zombify_settings.items[$(this).data('rel')].fontFamily = $(this).val();
            zf_draw();
        });

        $('.zf-opt_strokeWidth').on('change', function () {
            zombify_settings.items[$(this).data('rel')].lineWidth = $(this).val();
            zf_draw();
        });

        $('.zf-opt_fontColor').on('change', function () {
            zombify_settings.items[$(this).data('rel')].color = $(this).val();
            zf_draw();
        });

        $('.zf-opt_strokeColor').on('change', function () {
            zombify_settings.items[$(this).data('rel')].shadow = $(this).val();
            zf_draw();
        });


        var zf_draw = function () {

            zombify_canvas = document.getElementById('zf-memecanvas');
            zf_context = zombify_canvas.getContext('2d');
            zombify_canvas.width = mmContainerWidth;
            zombify_canvas.height = mmContainerWidth / delta;

            // zf_draw image
            zf_context.drawImage(img, 0, 0, mmContainerWidth, mmContainerWidth / delta);

            for (var i in zombify_settings.items) {
                if (zombify_settings.items.hasOwnProperty(i)) {
                    zf_context.fillStyle = zombify_settings.items[i].color;
                    zf_context.font = zombify_settings.items[i].fontSize + "px " + zombify_settings.items[i].fontFamily;
                    zf_context.strokeStyle = zombify_settings.items[i].shadow;
                    zf_context.lineWidth = zombify_settings.items[i].lineWidth;
                    zf_context.textAlign = 'center';
                    zf_context.textBaseline = 'top';
                    zf_context.lineJoin = 'round';

                    wrapText(zf_context, zombify_settings.items[i].text, zombify_settings.items[i].left + (zombify_settings.items[i].width / 2), zombify_settings.items[i].top, i);
                    for (var ii in zombify_settings.items[i].texts) {
                        if (zombify_settings.items[i].texts.hasOwnProperty(ii)) {

                            zf_context.font = zombify_settings.items[i].fontSize + "px " + zombify_settings.items[i].fontFamily;

                            if (zombify_settings.items[i].lineWidth != 0) {
                                zf_context.strokeText(zombify_settings.items[i].texts[ii].t, zombify_settings.items[i].left + (zombify_settings.items[i].width / 2), zombify_settings.items[i].texts[ii].top);
                            }
                            zf_context.fillText(zombify_settings.items[i].texts[ii].t, zombify_settings.items[i].left + (zombify_settings.items[i].width / 2), zombify_settings.items[i].texts[ii].top);

                        }
                    }
                }
            }
        }

        function wrapText(zf_context, text, x, top, i) {
            var words = text.split(' ');
            var line = '';
            var count = 1;
            var doredraw = false;
            var last = false;
            var y = top;
            var currFont = parseInt(zombify_settings.items[i].fontSize);
            var lineHeight = parseInt(zombify_settings.items[i].fontSize);

            zf_context.font = zombify_settings.items[i].fontSize + "px " + zombify_settings.items[i].fontFamily;

            zombify_settings.items[i].texts = {}


            for (var n = 0; n < words.length; n++) {
                var testLine = line + words[n] + ' ';
                var metrics = zf_context.measureText(testLine);
                var testWidth = metrics.width;
                zombify_settings.items[i].texts[count] = {}


                if (testWidth > zombify_settings.items[i].width && n > 0) {
                    zombify_settings.items[i].texts[count].t = line;
                    zombify_settings.items[i].texts[count].top = y;
                    line = words[n] + ' ';
                    y += lineHeight;
                    count++;

                    if (n == words.length - 1) last = true;
                } else {
                    line = testLine;
                    zombify_settings.items[i].texts[count].t = line;
                    zombify_settings.items[i].texts[count].top = y;

                }
                if ((n == words.length - 1) && last) {
                    zombify_settings.items[i].texts[count] = {}
                    zombify_settings.items[i].texts[count].t = words[n];
                    zombify_settings.items[i].texts[count].top = y;
                }
            }
            if (zombify_settings.items[i].fontType == 'auto') {
                if ((count * lineHeight) > zombify_settings.items[i].height) {

                    zombify_settings.items[i].fontSize = zombify_settings.items[i].height / count;
                    zf_context.font = parseInt(zombify_settings.items[i].fontSize) + "px" + zombify_settings.items[i].fontFamily;


                } else if (((count + 1) * lineHeight) < zombify_settings.items[i].height) {

                    zombify_settings.items[i].fontSize = currFont + 1;
                    zf_context.font = parseInt(zombify_settings.items[i].fontSize) + "px" + zombify_settings.items[i].fontFamily;
                    doredraw = true;
                }

                if (doredraw)  wrapText(zf_context, text, x, top, i);
            }
        }


        $('.zf-drag').on("click", function (e) {
            e.stopPropagation();
            $('.zf-topIndex').removeClass('zf-topIndex');
            $(this).parent().find('.zf-write').show().select();
            $(this).parent().addClass('zf-topIndex');
        });

        $('body').on("click", function () {
            $('.zf-topIndex').removeClass('zf-topIndex');
        });

        $('.zf-options_toggle').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).parent().toggleClass('zf-open');
        });

        $('.zf-options').on('click', function (e) {
            e.stopPropagation();
        });

        $('#zf-memecontainer').on('click', function (e) {
            // e.stopPropagation();
        });

        $('.zf-colorPicker .zf-current_color').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).parent().toggleClass('zf-active');
        });

        $('.zf-colorPicker .zf-color').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var color = $(this).attr('rel');
            $(this).parent().parent().find('.zf-color_input').val(color).trigger('change');
            $(this).parent().parent().find('.zf-current_color').css('background-color', color)
        });
        $('.zf-options').on('click', function (e) {
            $(this).find('.zf-active').removeClass('zf-active');
        });
    }

    $(window).resize(function () {
        mmContainerWidth = $('#zf-main').innerWidth();

        globalRatio = zombify_settings.global.width / mmContainerWidth;
        zombify_settings.global.width = mmContainerWidth;

        zombify_settings.items['1'].top = zombify_settings.items['1'].top / globalRatio;
        zombify_settings.items['1'].left = zombify_settings.items['1'].left / globalRatio;
        zombify_settings.items['1'].width = zombify_settings.items['1'].width / globalRatio;
        zombify_settings.items['1'].height = zombify_settings.items['1'].height / globalRatio;
        zombify_settings.items['1'].fontSize = zombify_settings.items['1'].fontSize / globalRatio;

        zombify_settings.items['2'].top = zombify_settings.items['2'].top / globalRatio;
        zombify_settings.items['2'].left = zombify_settings.items['2'].left / globalRatio;
        zombify_settings.items['2'].width = zombify_settings.items['2'].width / globalRatio;
        zombify_settings.items['2'].height = zombify_settings.items['2'].height / globalRatio;
        zombify_settings.items['2'].fontSize = zombify_settings.items['2'].fontSize / globalRatio;

        if (!zf_isMobile) {
            $('.zf-options').css('max-width', mmContainerWidth - 30 + 'px');
            $(".zf-drag-area[data-rel='1']").css({
                'top': zombify_settings.items['1'].top,
                'left': zombify_settings.items['1'].left,
                'width': zombify_settings.items['1'].width,
                'height': zombify_settings.items['1'].height
            });
            $(".zf-drag-area[data-rel='2']").css({
                'top': zombify_settings.items['2'].top,
                'left': zombify_settings.items['2'].left,
                'width': zombify_settings.items['2'].width,
                'height': zombify_settings.items['2'].height
            });
        }
    });

}

function createImageDataPressed() {
    var imageDataDisplay = document.getElementById('zf-readyImage');

    if( zombify_canvas !== undefined ) {

        if( zombify_meme_img_delta > 1 ){
            zf_quality = 1;
        } else {
            if(zombify_meme_img_delta<0.1){
                zf_quality = 0.1;
            } else {
                zf_quality = zombify_meme_img_delta;
            }
        }

        imageDataDisplay.value = zombify_canvas.toDataURL('image/jpeg', 1.0);

        return true;
    }

    return false;
}

function encodeObj(obj) {

    string = JSON.stringify(obj);

    // Create Base64 Object
    var Base64 = {
        _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=", encode: function (e) {
            var t = "";
            var n, r, i, s, o, u, a;
            var f = 0;
            e = Base64._utf8_encode(e);
            while (f < e.length) {
                n = e.charCodeAt(f++);
                r = e.charCodeAt(f++);
                i = e.charCodeAt(f++);
                s = n >> 2;
                o = (n & 3) << 4 | r >> 4;
                u = (r & 15) << 2 | i >> 6;
                a = i & 63;
                if (isNaN(r)) {
                    u = a = 64
                } else if (isNaN(i)) {
                    a = 64
                }
                t = t + this._keyStr.charAt(s) + this._keyStr.charAt(o) + this._keyStr.charAt(u) + this._keyStr.charAt(a)
            }
            return t
        }, decode: function (e) {
            var t = "";
            var n, r, i;
            var s, o, u, a;
            var f = 0;
            e = e.replace(/[^A-Za-z0-9+/=]/g, "");
            while (f < e.length) {
                s = this._keyStr.indexOf(e.charAt(f++));
                o = this._keyStr.indexOf(e.charAt(f++));
                u = this._keyStr.indexOf(e.charAt(f++));
                a = this._keyStr.indexOf(e.charAt(f++));
                n = s << 2 | o >> 4;
                r = (o & 15) << 4 | u >> 2;
                i = (u & 3) << 6 | a;
                t = t + String.fromCharCode(n);
                if (u != 64) {
                    t = t + String.fromCharCode(r)
                }
                if (a != 64) {
                    t = t + String.fromCharCode(i)
                }
            }
            t = Base64._utf8_decode(t);
            return t
        }, _utf8_encode: function (e) {
            e = e.replace(/rn/g, "n");
            var t = "";
            for (var n = 0; n < e.length; n++) {
                var r = e.charCodeAt(n);
                if (r < 128) {
                    t += String.fromCharCode(r)
                } else if (r > 127 && r < 2048) {
                    t += String.fromCharCode(r >> 6 | 192);
                    t += String.fromCharCode(r & 63 | 128)
                } else {
                    t += String.fromCharCode(r >> 12 | 224);
                    t += String.fromCharCode(r >> 6 & 63 | 128);
                    t += String.fromCharCode(r & 63 | 128)
                }
            }
            return t
        }, _utf8_decode: function (e) {
            var t = "";
            var n = 0;
            var r = c1 = c2 = 0;
            while (n < e.length) {
                r = e.charCodeAt(n);
                if (r < 128) {
                    t += String.fromCharCode(r);
                    n++
                } else if (r > 191 && r < 224) {
                    c2 = e.charCodeAt(n + 1);
                    t += String.fromCharCode((r & 31) << 6 | c2 & 63);
                    n += 2
                } else {
                    c2 = e.charCodeAt(n + 1);
                    c3 = e.charCodeAt(n + 2);
                    t += String.fromCharCode((r & 15) << 12 | (c2 & 63) << 6 | c3 & 63);
                    n += 3
                }
            }
            return t
        }
    }


    // Encode the String
    var encodedString = Base64.encode(string);

    return encodedString;

}

function decodeObj(string) {

    // Create Base64 Object
    var Base64 = {
        _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=", encode: function (e) {
            var t = "";
            var n, r, i, s, o, u, a;
            var f = 0;
            e = Base64._utf8_encode(e);
            while (f < e.length) {
                n = e.charCodeAt(f++);
                r = e.charCodeAt(f++);
                i = e.charCodeAt(f++);
                s = n >> 2;
                o = (n & 3) << 4 | r >> 4;
                u = (r & 15) << 2 | i >> 6;
                a = i & 63;
                if (isNaN(r)) {
                    u = a = 64
                } else if (isNaN(i)) {
                    a = 64
                }
                t = t + this._keyStr.charAt(s) + this._keyStr.charAt(o) + this._keyStr.charAt(u) + this._keyStr.charAt(a)
            }
            return t
        }, decode: function (e) {
            var t = "";
            var n, r, i;
            var s, o, u, a;
            var f = 0;
            e = e.replace(/[^A-Za-z0-9+/=]/g, "");
            while (f < e.length) {
                s = this._keyStr.indexOf(e.charAt(f++));
                o = this._keyStr.indexOf(e.charAt(f++));
                u = this._keyStr.indexOf(e.charAt(f++));
                a = this._keyStr.indexOf(e.charAt(f++));
                n = s << 2 | o >> 4;
                r = (o & 15) << 4 | u >> 2;
                i = (u & 3) << 6 | a;
                t = t + String.fromCharCode(n);
                if (u != 64) {
                    t = t + String.fromCharCode(r)
                }
                if (a != 64) {
                    t = t + String.fromCharCode(i)
                }
            }
            t = Base64._utf8_decode(t);
            return t
        }, _utf8_encode: function (e) {
            e = e.replace(/rn/g, "n");
            var t = "";
            for (var n = 0; n < e.length; n++) {
                var r = e.charCodeAt(n);
                if (r < 128) {
                    t += String.fromCharCode(r)
                } else if (r > 127 && r < 2048) {
                    t += String.fromCharCode(r >> 6 | 192);
                    t += String.fromCharCode(r & 63 | 128)
                } else {
                    t += String.fromCharCode(r >> 12 | 224);
                    t += String.fromCharCode(r >> 6 & 63 | 128);
                    t += String.fromCharCode(r & 63 | 128)
                }
            }
            return t
        }, _utf8_decode: function (e) {
            var t = "";
            var n = 0;
            var r = c1 = c2 = 0;
            while (n < e.length) {
                r = e.charCodeAt(n);
                if (r < 128) {
                    t += String.fromCharCode(r);
                    n++
                } else if (r > 191 && r < 224) {
                    c2 = e.charCodeAt(n + 1);
                    t += String.fromCharCode((r & 31) << 6 | c2 & 63);
                    n += 2
                } else {
                    c2 = e.charCodeAt(n + 1);
                    c3 = e.charCodeAt(n + 2);
                    t += String.fromCharCode((r & 15) << 12 | (c2 & 63) << 6 | c3 & 63);
                    n += 3
                }
            }
            return t
        }
    }


    // Decode the String
    var decodedString = Base64.decode(string);
    decodedString = JSON.parse(decodedString);

    return decodedString;

}
