import React from 'react';
import { ChevronRight } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import gsap from 'gsap';

const CategoryCard = ({ category }) => {
    const navigate = useNavigate();
    const cardRef = React.useRef(null);
    const borderRef = React.useRef(null);

    const onMouseEnter = () => {
        gsap.to(cardRef.current.querySelector('img'), {
            scale: 1.1,
            duration: 0.5,
            ease: 'power2.out'
        });
        gsap.to(borderRef.current, {
            opacity: 1,
            scale: 1,
            duration: 0.3,
            ease: 'power2.out'
        });
    };

    const onMouseLeave = () => {
        gsap.to(cardRef.current.querySelector('img'), {
            scale: 1,
            duration: 0.5,
            ease: 'power2.in'
        });
        gsap.to(borderRef.current, {
            opacity: 0,
            scale: 0.95,
            duration: 0.3,
            ease: 'power2.in'
        });
    };

    const [imageError, setImageError] = React.useState(false);

    const renderImage = () => {
        if (category.image && !imageError) {
            return (
                <img
                    src={category.image}
                    alt={category.name}
                    className="w-full h-full object-cover transition-transform duration-700"
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
            onClick={() => navigate(`/category/${category.id}`)}
            className="relative cursor-pointer group"
        >
            <div className="relative h-48 md:h-64 rounded-3xl overflow-hidden shadow-lg mb-4 bg-zinc-100 dark:bg-zinc-800">
                {renderImage()}
                {/* Border Highlight Overlay */}
                <div
                    ref={borderRef}
                    className="absolute inset-0 border-4 border-cafe-primary dark:border-white opacity-0 scale-[0.98] rounded-3xl transition-all pointer-events-none"
                ></div>
            </div>

            <div className="px-2 text-center">
                <h3 className="text-xl font-bold dark:text-cafe-text-dark group-hover:text-cafe-primary transition-colors">{category.name}</h3>
                <p className="text-xs opacity-50 mt-1 uppercase tracking-widest">{category.group}</p>
            </div>
        </div>
    );
};

export default CategoryCard;
