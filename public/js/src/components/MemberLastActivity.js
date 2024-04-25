import React from 'react'


const MemberLastActivity = ({ activity }) => {
    const createUnixTimestamp = (date) => {
        const timestamp = new Date(date).getTime(); // Get timestamp in milliseconds

        return Math.floor(timestamp / 1000); // Convert milliseconds to seconds (UNIX timestamp)
    };

    const isMemberOnline = () => {
        const lastActivityTimeStamp = createUnixTimestamp(activity.date);
        const activityTimeframe = 5 * 60;
        const currentTime = Math.floor(Date.now() / 1000); // Convert milliseconds to seconds   

        return currentTime - lastActivityTimeStamp <= activityTimeframe;
    };

    
    return (
        <>
            {isMemberOnline() ? (
                <div className="member-status online">Online</div>
            ) : (
                <div className="member-status offline">Offline</div>
            )}
        </>
    );
}


export default MemberLastActivity;