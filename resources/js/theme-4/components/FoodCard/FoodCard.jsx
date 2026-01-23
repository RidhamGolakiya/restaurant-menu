import React from 'react';
import { Star, Plus } from 'lucide-react';
import gsap from 'gsap';
import { useRestaurantData } from '../../hooks/useRestaurantData';

const FoodCard = ({ food, onClick }) => {
    const { restaurant } = useRestaurantData();
    const currency = restaurant?.currency || '$';
    const cardRef = React.useRef(null);
    const imageRef = React.useRef(null);

    const onMouseEnter = () => {
        gsap.to(cardRef.current, {
            y: -10,
            boxShadow: '0 20px 40px rgba(212, 163, 115, 0.15)',
            duration: 0.3,
            ease: 'power2.out'
        });
        gsap.to(imageRef.current, {
            scale: 1.1,
            duration: 0.5,
            ease: 'power2.out'
        });
    };

    const onMouseLeave = () => {
        gsap.to(cardRef.current, {
            y: 0,
            boxShadow: '0 4px 6px rgba(0, 0, 0, 0.05)',
            duration: 0.3,
            ease: 'power2.in'
        });
        gsap.to(imageRef.current, {
            scale: 1,
            duration: 0.5,
            ease: 'power2.in'
        });
    };

    const [imageError, setImageError] = React.useState(false);

    const renderImage = () => {
        if (food.image && !imageError) {
            return (
                <img
                    ref={imageRef}
                    src={food.image}
                    alt={food.name}
                    className="w-full h-full object-cover"
                    onError={() => setImageError(true)}
                />
            );
        }
        return (
            <div className="w-full h-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center opacity-60 transition-colors">
                <span className="text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-[0.2em] text-[10px]">No Image</span>
            </div>
        );
    };

    return (
        <div
            ref={cardRef}
            onMouseEnter={onMouseEnter}
            onMouseLeave={onMouseLeave}
            onClick={() => onClick(food)}
            className="bg-white dark:bg-zinc-800 rounded-3xl overflow-hidden cursor-pointer transition-colors duration-300 shadow-sm border border-transparent hover:border-cafe-primary/20 group h-full flex flex-col"
        >
            <div className="relative h-64 overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                {renderImage()}
                {food.bestSelling && (
                    <div className="absolute top-4 left-4 px-3 py-1 bg-cafe-primary text-white text-xs font-bold rounded-full uppercase tracking-tighter">
                        Best Seller
                    </div>
                )}
            </div>

            <div className="p-6 flex-grow flex flex-col justify-between">
                <div>
                    <div className="flex justify-between items-start mb-2">
                        <h3 className="text-xl font-bold dark:text-cafe-text-dark leading-tight">{food.name}</h3>
                        <span className="text-xl font-bold text-cafe-primary">{currency}{food.price.toFixed(2)}</span>
                    </div>

                    <div className="flex items-center gap-1 mb-3">
                        <Star size={14} className="fill-cafe-primary text-cafe-primary" />
                        <span className="text-sm font-semibold opacity-70">{food.rating}</span>
                    </div>

                    <p className="text-sm opacity-60 line-clamp-2 leading-relaxed">
                        {food.description}
                    </p>
                </div>
            </div>
        </div>
    );
};

export default FoodCard;
