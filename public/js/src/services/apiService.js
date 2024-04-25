const BASE_URL = bffs_ajax_object.root;

// Function to fetch members from BuddyPress API using fetch with configurable options
export const fetchMembers = async (args) => {
    try {
        // Construct the API URL with query parameters
        const apiUrl = `${BASE_URL}buddypress/v1/members?include=${args.include.join(',')}&exclude=${args.exclude.join(',')}&per_page=${args.per_page}&populate_extras=${true}`;
        
        // Configure fetch options
        const fetchOptions = {
            method: 'GET',
            headers: {
                // Add headers if needed (e.g., authorization token)
                'Content-Type': 'application/json',
                'X-WP-Nonce': bffs_ajax_object.nonce, // WordPress nonce for security (if available)
            },
            // You can add more options like credentials, cache, etc.
            // credentials: 'same-origin', // Include cookies in the request
        };

        // Send fetch request
        const response = await fetch(apiUrl, fetchOptions);

        // Check if response is successful
        if (!response.ok) {
            throw new Error(`Failed to fetch members: ${response.status} ${response.statusText}`);
        }

        // Parse response body as JSON
        const members = await response.json();
        return members; // Return the fetched members data
    } catch (error) {
        console.error('Error fetching members:', error);
        throw error; // Rethrow the error for handling in the component
    }
};


export const removeMember = async (memberId) => {
    try {
        // Construct the API URL to get the user data
        const apiUrl = `${BASE_URL}bpffs/v1/swipe/${memberId}?security=${bffs_ajax_object.security}`;

        // Configure fetch options to update user meta
        const updateOptions = {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': bffs_ajax_object.nonce,
            }
        };

        // Send fetch request to update user meta
        const response = await fetch(apiUrl, updateOptions);
        // Check if update response is successful
        if (response.ok) {
            return true; // Return true indicating success
        } else {
            return false; // Return false indicating failure
        }
    } catch (error) {
        console.error('Error updating user meta:', error);
        throw error; // Rethrow the error for handling in the component
    }
}



export const friendShip = async (memberID) => {
    try {
        // Construct the API URL with query parameters
        const apiUrl = `${BASE_URL}buddypress/v1/friends`;

        // Configure fetch options
        const fetchOptions = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': bffs_ajax_object.nonce,
            },
            body: JSON.stringify({
                'initiator_id': bffs_ajax_object.userId,
                'friend_id': memberID
            }),
        };

        // Send fetch request
        const response = await fetch(apiUrl, fetchOptions);
        
        // Check if response is successful
        if (response.ok) {
            return true; // Return true indicating success
        } else {
            return false; // Return false indicating failure
        }
    } catch (error) {
        console.error('Error to swipe member', error);
        throw error; // Rethrow the error for handling in the component
    }
}