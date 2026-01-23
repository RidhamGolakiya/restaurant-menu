import React, { useEffect, useRef, useState } from 'react';
import { useRestaurantData } from '../hooks/useRestaurantData';
import { Link } from 'react-router-dom';
import { ChevronLeft, Sparkles, Star } from 'lucide-react';
import gsap from 'gsap';
import FoodModal from '../components/FoodModal/FoodModal';

const SpecialsPage = () => {
    const { specials, loading } = useRestaurantData();
    const [selectedFood, setSelectedFood] = useState(null);
    const pageRef = useRef(null);
    const cardsRef = useRef([]);

    useEffect(() => {
        window.scrollTo(0, 0);

        const tl = gsap.timeline();

        tl.fromTo(pageRef.current.querySelector('.page-header'),
            { y: 30, opacity: 0 },
            { y: 0, opacity: 1, duration: 0.8, ease: 'power3.out' }
        );

        if (cardsRef.current.length > 0) {
            tl.fromTo(cardsRef.current,
                { y: 50, opacity: 0 },
                { y: 0, opacity: 1, duration: 0.6, stagger: 0.1, ease: 'power3.out' },
                "-=0.4"
            );
        }
    }, []);

    return (
        <div ref={pageRef} className="min-h-screen pt-32 pb-20 bg-white dark:bg-cafe-dark transition-colors duration-500">
            <div className="container mx-auto px-6">
                {/* Page Header */}
                <div className="page-header mb-16">
                    <Link to="/" className="inline-flex items-center gap-2 text-cafe-primary font-bold mb-8 hover:gap-3 transition-all">
                        <ChevronLeft size={20} /> Back to Home
                    </Link>

                    <div className="flex flex-col md:flex-row md:items-end justify-between gap-6">
                        <div>
                            <div className="flex items-center gap-2 text-cafe-primary mb-4">
                                <Sparkles size={20} />
                                <span className="font-bold tracking-[0.3em] uppercase text-xs">Limited Time</span>
                            </div>
                            <h1 className="text-5xl md:text-6xl font-black dark:text-cafe-text-dark tracking-tighter">
                                Chef's <span className="text-cafe-primary">Specials</span>
                            </h1>
                        </div>
                        <p className="max-w-md opacity-60 text-lg leading-relaxed">
                            Hand-crafted seasonal masterpieces, curated by our head chef to elevate your coffee experience.
                        </p>
                    </div>
                </div>

                {/* Specials Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    {specials && specials.map((food, index) => (
                        <div
                            key={food.id}
                            ref={el => cardsRef.current[index] = el}
                            className="group"
                        >
                            <div className="bg-white dark:bg-zinc-900/50 rounded-[2.5rem] overflow-hidden border border-black/5 dark:border-white/5 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 flex flex-col h-full">
                                {/* Image Container */}
                                <div className="relative h-72 overflow-hidden">
                                    <img
                                        src={food.image}
                                        alt={food.name}
                                        className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                    />
                                    <div className="absolute top-4 left-4 px-4 py-2 bg-cafe-primary text-white text-[10px] font-black rounded-full uppercase tracking-widest shadow-lg">
                                        {food.tag || 'Special'}
                                    </div>
                                    <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-end p-6">
                                        <button
                                            onClick={() => setSelectedFood(food)}
                                            className="w-full py-3 bg-white text-black font-black text-xs uppercase tracking-widest rounded-xl hover:bg-cafe-primary hover:text-white transition-colors"
                                        >
                                            View Details
                                        </button>
                                    </div>
                                </div>

                                {/* Content */}
                                <div className="p-8 flex-grow flex flex-col">
                                    <div className="flex justify-between items-start mb-4">
                                        <h3 className="text-2xl font-bold dark:text-cafe-text-dark group-hover:text-cafe-primary transition-colors">{food.name}</h3>
                                        <span className="text-2xl font-black text-cafe-primary">${food.price.toFixed(2)}</span>
                                    </div>

                                    <div className="flex items-center gap-1 mb-4">
                                        {[...Array(5)].map((_, i) => (
                                            <Star key={i} size={14} className={i < Math.floor(food.rating) ? "fill-cafe-primary text-cafe-primary" : "text-zinc-300 dark:text-zinc-700"} />
                                        ))}
                                        <span className="text-sm font-bold opacity-40 ml-2">{food.rating}</span>
                                    </div>

                                    <p className="opacity-60 text-sm leading-relaxed line-clamp-3 mb-8">
                                        {food.description}
                                    </p>

                                    <div className="mt-auto">
                                        <button
                                            onClick={() => setSelectedFood(food)}
                                            className="w-full py-4 bg-cafe-primary/10 text-cafe-primary font-black text-xs uppercase tracking-[0.2em] rounded-2xl hover:bg-cafe-primary hover:text-white transition-all duration-300"
                                        >
                                            Reveal Recipe
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>

            <FoodModal food={selectedFood} onClose={() => setSelectedFood(null)} />
        </div>
    );
};

export default SpecialsPage;
