import React from 'react';
import { useSwiper } from 'swiper/react';
import IconCross from './IconCross';
import IconUserPlus from './IconUserPlus';

const SwiperNavButtons = () => {
    const swiper = useSwiper();

    return (
        <div className="swiper-nav-buttons">
            <button className="swipe-friends" onClick={() => swiper.slidePrev()}>
                <IconUserPlus />
            </button>
            <button className="swipe-remove" onClick={() => swiper.slideNext()}>
                <IconCross />
            </button>
        </div>
    );
}

export default SwiperNavButtons;