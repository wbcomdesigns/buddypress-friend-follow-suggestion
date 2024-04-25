import React from 'react'
import IconDiscover from './IconDiscover';
import IconEnvelope from './IconEnvelope';
import IconUserProfile from './IconUserProfile';

const MemberAction = ({ member }) => {
    const composeMessageUrl = bffs_ajax_object.compose + member.user_login;

    return (
        <ul>
            <li>
                <a href={member.link}>
                    <IconDiscover />
                </a>
            </li>
            <li>
                <a href={composeMessageUrl}>
                    <IconEnvelope/>
                </a>
            </li>
            <li>
                <a href={member.link +'profile'}>
                    <IconUserProfile />
                </a>
            </li>
        </ul>
    );
}

export default MemberAction;