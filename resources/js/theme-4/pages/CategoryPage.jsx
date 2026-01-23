import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import FoodCard from '../components/FoodCard/FoodCard';
import FoodModal from '../components/FoodModal/FoodModal';
import { ChevronLeft } from 'lucide-react';
import gsap from 'gsap';
import { useRestaurantData } from '../hooks/useRestaurantData';

const CategoryPage = () => {
    const { id } = useParams();
    const navigate = useNavigate();
    const { categories, foods, loading } = useRestaurantData();
    const [selectedFood, setSelectedFood] = useState(null);

    const category = categories.find(c => c.id === id);
    const categoryFoods = foods.filter(f => f.categoryId === id);

    useEffect(() => {
        window.scrollTo(0, 0);
        if (!loading) {
            gsap.fromTo('.page-entry',
                { opacity: 0, y: 30 },
                { opacity: 1, y: 0, duration: 0.8, stagger: 0.1, ease: 'power2.out' }
            );
        }
    }, [id, loading]);

    if (loading) return <div className="p-20 text-center">Loading...</div>;
    if (!category) return <div className="p-20 text-center">Category not found</div>;

    return (
        <div className="pt-32 pb-20 container mx-auto px-6 min-h-screen">
            <div className="flex flex-col md:flex-row justify-between items-start md:items-end mb-16 gap-8">
                <div className="page-entry">
                    <button
                        onClick={() => navigate('/')}
                        className="flex items-center gap-2 font-bold mb-6 text-cafe-text/40 dark:text-cafe-text-dark/40 hover:text-cafe-primary transition-colors group"
                    >
                        <ChevronLeft size={18} /> Back to Home
                    </button>
                    <h1 className="text-5xl font-black dark:text-cafe-text-dark tracking-tighter">{category.name}</h1>
                    <p className="text-lg opacity-60 mt-2">{category.group} Collection</p>
                </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                {categoryFoods.length > 0 ? (
                    categoryFoods.map(food => (
                        <div key={food.id} className="page-entry">
                            <FoodCard food={food} onClick={setSelectedFood} />
                        </div>
                    ))
                ) : (
                    <div className="col-span-full py-20 text-center">
                        <p className="text-xl opacity-40 italic">No items found in this category.</p>
                    </div>
                )}
            </div>

            <FoodModal food={selectedFood} onClose={() => setSelectedFood(null)} />
        </div>
    );
};

export default CategoryPage;
