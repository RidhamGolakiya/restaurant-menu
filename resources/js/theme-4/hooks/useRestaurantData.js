import { useState, useEffect } from 'react';

export const useRestaurantData = () => {
    const [data, setData] = useState({
        restaurant: null,
        categories: [],
        foods: [],
        specials: [],
        offers: [],
        reviews: [],
        products: [],
        settings: {},
        loading: true
    });

    useEffect(() => {
        if (window.RESTAURANT_DATA) {
            setData({
                ...window.RESTAURANT_DATA,
                loading: false
            });
        }
    }, []);

    return data;
};
