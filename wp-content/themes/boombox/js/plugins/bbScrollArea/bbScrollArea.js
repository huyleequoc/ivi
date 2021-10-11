(function ($) {
    jQuery.fn.bbScrollableArea = function () {

        return this.each(function () {

            // Variables
            var $this = jQuery(this),
                $child = $this.find('ul'),
                $nextBtn = '<a href="#" class="bb-nav bb-arrow-next" title="Next"></a>',
                $prevBtn = '<a href="#" class="bb-nav bb-arrow-prev" title="Prev"></a>',
                scroll = 0,
                delta = 300, // scroll step
                maxScrollLeft = 0; // max scroll area

            var BB = {
                init: function () {
                    if (($child[0].scrollWidth > $child[0].clientWidth)) {
                        $this.addClass('bb-scroll');
                        maxScrollLeft = $child[0].scrollWidth - $child[0].clientWidth;
                        if ($child.scrollLeft() === 0) $this.addClass('bb-scroll-start');

                        // Add arrows if need
                        if (!$this.find('.bb-nav').length) {
                            $this.append(jQuery($nextBtn));
                            $this.append(jQuery($prevBtn));
                            BB.actions();
                        }
                    } else {
                        $this.removeClass('bb-scroll');
                        $this.find('.bb-nav').remove();
                    }
                },
                next: function () {
                    BB.elementScroll(scroll + delta)
                    // After next coll BB.scroll()
                },
                prev: function () {
                    BB.elementScroll(scroll - delta)
                    // After prev coll BB.scroll()
                },
                scroll: function () {
                    BB.afterAction();
                },
                elementScroll: function (position) {
                    $child.animate({
                        scrollLeft: position
                    }, 500);
                },
                actions: function () {
                    // Next/Prev/Scroll Actions
                    $this.find('.bb-arrow-next').on('click', function (e) {
                        e.preventDefault();
                        BB.next();
                    });
                    $this.find('.bb-arrow-prev').on('click', function (e) {
                        e.preventDefault();
                        BB.prev();
                    });
                    $child.on('scroll', function () {
                        BB.scroll();
                    });
                },
                afterAction: function () {

                    // if scroll in the start position
                    if ($child.scrollLeft() === 0) {
                        $this.addClass('bb-scroll-start');
                        $this.removeClass('bb-scroll-end');
                        scroll = 0;

                        // if scroll in the end position
                    } else if ($child.scrollLeft() >= maxScrollLeft) {
                        $this.addClass('bb-scroll-end');
                        $this.removeClass('bb-scroll-start');
                        scroll = maxScrollLeft;
                    } else {
                        // if scroll in the middle
                        $this.removeClass('bb-scroll-start');
                        $this.removeClass('bb-scroll-end');
                        scroll = $child.scrollLeft();
                    }
                }
            };

            // Plugin init
            BB.init();

            // Windows resize coll plugin init for reconstruct
            jQuery(window).resize(function () {
                BB.init();
            });
        });
    };
}(jQuery));
