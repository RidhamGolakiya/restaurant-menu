import React from 'react';

const FloatingButtons = () => {
    const { zomato_link, swiggy_link } = window.RESTAURANT_DATA?.restaurant || {};

    if (!zomato_link && !swiggy_link) return null;

    return (
        <div className="fixed bottom-6 right-6 z-50 flex flex-col gap-3">
            {zomato_link && (
                <a
                    href={zomato_link}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="bg-white hover:bg-gray-100 text-white font-bold rounded-full shadow-lg transition-transform transform hover:scale-105 flex items-center justify-center w-12 h-12 overflow-hidden border border-gray-200"
                    title="Order on Zomato"
                >
                    <img src="/images/zomato.png" alt="Zomato" className="w-full h-full object-cover" />
                </a>
            )}
            {swiggy_link && (
                <a
                    href={swiggy_link}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="bg-white hover:bg-gray-100 text-white font-bold rounded-full shadow-lg transition-transform transform hover:scale-105 flex items-center justify-center w-12 h-12 overflow-hidden border border-gray-200"
                    title="Order on Swiggy"
                >
                    <img src="/images/swiggy.png" alt="Swiggy" className="w-full h-full object-cover" />
                </a>
            )}
        </div>
    );
};

export default FloatingButtons;
