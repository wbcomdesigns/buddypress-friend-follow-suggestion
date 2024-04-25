import React, { useState, useEffect, useRef } from 'react';
import { Swiper, SwiperSlide } from 'swiper/react';
import { EffectCreative, Manipulation, Navigation } from 'swiper/modules';
import 'swiper/scss';
import 'swiper/scss/effect-creative';
import 'swiper/scss/navigation';
import { fetchMembers, removeMember, friendShip } from '../services/apiService';
import SwiperNavButtons  from './SwiperNavButtons';
import MemberLastActivity from './MemberLastActivity';
import MemberAction from './MemberAction';


const Slider = ({ args }) => {

    const [members, setMembers] = useState([]);
    const swiperRef = useRef(null);
    const appendedSlideRef = useRef(false); // Flag to track whether slide has been appended

    useEffect(() => {
        const fetchData = async () => {
            try {
                const membersData = await fetchMembers(args);
                setMembers(membersData);
            } catch (error) {
                console.error('Error loading members:', error);
            }
        };

        fetchData();
    }, [args]);

    const initialSlideIndex = () => {
        if (members.length > 0) {
            return Math.round(members.length / 2);
        }
    };

    const handleSlideChange = async (swiper) => {
        const { activeIndex, previousIndex } = swiper;

        if (activeIndex > previousIndex) {
            try {
                const memberToRemove = members[previousIndex];
                const isRemoved = await removeMember(memberToRemove.id);
                if (isRemoved) {
                    setMembers((prevMembers) => prevMembers.filter((_, index) => index !== previousIndex));
                }
            } catch (error) {
                console.error('Error removing slide:', error);
            }
        } else if (activeIndex < previousIndex) {
            try {
                const memberToRemove = members[previousIndex];
                const isFriends = await friendShip(memberToRemove.id);

                if (isFriends) {
                    setMembers((prevMembers) => prevMembers.filter((_, index) => index !== previousIndex));
                }
            } catch (error) {
                console.error('Error removing slide:', error);
            }
        }
    };


    const handleReachEnd = async (swiper) => {
        if (!appendedSlideRef.current && members.length > 0) {
            // Append new slide only if it hasn't been appended yet and members array is not empty
            swiper.appendSlide(
                `<div class="swiper-slide">You have reached the maximum number of suggestions.</div>`
            );
            appendedSlideRef.current = true; // Set flag to true after appending the slide
        }
    };

    return (
        <Swiper
            ref={swiperRef}
            grabCursor={true}
            effect={'creative'}
            creativeEffect={{
                prev: {
                    shadow: true,
                    translate: ['-120%', 0, -500],
                },
                next: {
                    shadow: true,
                    translate: ['120%', 0, -500],
                },
            }}
            allowSlidePrev={true}
            modules={[EffectCreative, Manipulation, Navigation]}
            initialSlide={initialSlideIndex()}
            onSlideChange={(swiper) => handleSlideChange(swiper)}
            onReachEnd={(swiper) => handleReachEnd(swiper)}
        >

            {members.map((member) => (
                <SwiperSlide key={member.id}>
                    <div className="item-entry">
                        <div className="list-wrap">
                            <div className="item-avatar">
                                <img src={member.avatar_urls.full} alt={member.name} />
                            </div>
                            <SwiperNavButtons />
                            <div className="item-title member-name">
                                <a href={member.link}>
                                    {member.name}
                                </a>

                                <MemberLastActivity 
                                    activity={member.last_activity} />
                                <MemberAction member={member} />
                            </div>
                        </div>
                    </div>
                </SwiperSlide>
            ))}
        </Swiper>
    );
};

export default Slider;
