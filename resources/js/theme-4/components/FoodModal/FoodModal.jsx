import React, { useEffect, useRef } from 'react';
import { X, Star, Leaf } from 'lucide-react';
import gsap from 'gsap';

const FoodModal = ({ food, onClose }) => {
    const modalRef = useRef(null);
    const overlayRef = useRef(null);
    const contentRef = useRef(null);

    useEffect(() => {
        if (food) {
            const tl = gsap.timeline();
            tl.fromTo(overlayRef.current, { opacity: 0 }, { opacity: 1, duration: 0.3 })
                .fromTo(contentRef.current, { scale: 0.8, opacity: 0, y: 50 }, { scale: 1, opacity: 1, y: 0, duration: 0.5, ease: 'back.out(1.7)' }, "-=0.2");
        }
    }, [food]);

    const handleClose = () => {
        const tl = gsap.timeline({ onComplete: onClose });
        tl.to(contentRef.current, { scale: 0.8, opacity: 0, y: 50, duration: 0.3, ease: 'power2.in' })
            .to(overlayRef.current, { opacity: 0, duration: 0.2 }, "-=0.1");
    };

    const [imageError, setImageError] = React.useState(false);

    // Reset error state when food changes
    useEffect(() => {
        setImageError(false);
    }, [food]);

    const renderImage = () => {
        if (food?.image && !imageError) {
            return (
                <img
                    src={food.image}
                    alt={food.name}
                    className="w-full h-full object-cover"
                    onError={() => setImageError(true)}
                />
            );
        }
        return (
            <div className="w-full h-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center opacity-60">
                <span className="text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-[0.2em] text-xs">No Image Available</span>
            </div>
        );
    };

    if (!food) return null;

    return (
        <div ref={modalRef} className="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div
                ref={overlayRef}
                onClick={handleClose}
                className="absolute inset-0 bg-black/60 backdrop-blur-sm"
            ></div>

            <div
                ref={contentRef}
                className="relative bg-white dark:bg-zinc-900 rounded-[2.5rem] overflow-hidden max-w-4xl w-full max-h-[90vh] flex flex-col md:flex-row shadow-2xl"
            >
                <button
                    onClick={handleClose}
                    className="absolute top-6 right-6 z-10 p-2 bg-white/20 backdrop-blur-md rounded-full text-white md:text-zinc-500 md:bg-zinc-100 md:dark:bg-zinc-800 md:dark:text-zinc-400 hover:rotate-90 transition-transform"
                >
                    <X size={24} />
                </button>

                <div className="w-full md:w-1/2 h-64 md:h-auto">
                    {renderImage()}
                </div>

                <div className="w-full md:w-1/2 p-8 md:p-12 overflow-y-auto">
                    <div className="inline-block px-3 py-1 bg-cafe-primary/10 text-cafe-primary text-xs font-bold rounded-full mb-4">
                        {food.categoryId.toUpperCase()}
                    </div>
                    <h2 className="text-3xl md:text-4xl font-bold mb-4 dark:text-cafe-text-dark">{food.name}</h2>

                    <div className="flex items-center gap-4 mb-6">
                        <span className="text-3xl font-bold text-cafe-primary">${food.price.toFixed(2)}</span>
                        <div className="flex items-center gap-1 px-2 py-1 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                            <Star size={16} className="fill-cafe-primary text-cafe-primary" />
                            <span className="font-bold">{food.rating}</span>
                        </div>
                    </div>

                    <p className="text-zinc-600 dark:text-zinc-400 leading-relaxed mb-8">
                        {food.description}
                    </p>

                    <div className="mb-8">
                        <h4 className="font-bold mb-4 flex items-center gap-2">
                            <Leaf size={18} className="text-cafe-accent" />
                            Ingredients
                        </h4>
                        <div className="flex flex-wrap gap-2">
                            {food.ingredients?.map((ing, i) => (
                                <span key={i} className="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 rounded-full text-sm opacity-80">
                                    {ing}
                                </span>
                            ))}
                        </div>
                    </div>


                </div>
            </div>
        </div>
    );
};

export default FoodModal;
