import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useRestaurantData } from '../hooks/useRestaurantData';
import { ChevronLeft, Trophy } from 'lucide-react';
import FoodModal from '../components/FoodModal/FoodModal';
import gsap from 'gsap';
import FoodCard from '../components/FoodCard/FoodCard';

const BestFoodPage = () => {
    const navigate = useNavigate();
    const { foods, loading } = useRestaurantData();
    const [selectedFood, setSelectedFood] = useState(null);
    const bestSellers = foods ? foods.filter(f => f.bestSelling) : [];

    useEffect(() => {
        window.scrollTo(0, 0);
        gsap.fromTo('.page-entry',
            { opacity: 0, y: 50 },
            { opacity: 1, y: 0, duration: 1, stagger: 0.15, ease: 'power3.out' }
        );
    }, []);

    return (
        <div className="pt-32 pb-20 container mx-auto px-6 min-h-screen">
            <div className="page-entry mb-16">
                <button
                    onClick={() => navigate('/')}
                    className="flex items-center gap-2 font-bold mb-6 text-cafe-text/40 dark:text-cafe-text-dark/40 hover:text-cafe-primary transition-colors group"
                >
                    <ChevronLeft size={18} /> Back to Home
                </button>
                <div className="relative">
                    <div className="absolute -top-10 -left-6 opacity-10 rotate-[-15deg]">
                        <Trophy size={120} className="text-cafe-primary" />
                    </div>
                    <h1 className="text-5xl font-black mb-4 dark:text-cafe-text-dark tracking-tighter">Signature Favorites</h1>
                    <p className="text-lg opacity-60 max-w-2xl">The dishes and drinks that defined our journey. Voted most loved by our loyal community.</p>
                </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                {bestSellers.map(food => (
                    <div key={food.id} className="page-entry">
                        <FoodCard food={food} onClick={setSelectedFood} />
                    </div>
                ))}
            </div>

            <FoodModal food={selectedFood} onClose={() => setSelectedFood(null)} />
        </div>
    );
};

export default BestFoodPage;
