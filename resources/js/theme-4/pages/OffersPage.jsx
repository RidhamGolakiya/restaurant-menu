import React, { useEffect } from 'react';
import { useRestaurantData } from '../hooks/useRestaurantData';
import { useNavigate } from 'react-router-dom';
import { ChevronLeft, Sparkles } from 'lucide-react';
import gsap from 'gsap';

const OffersPage = () => {
    const navigate = useNavigate();
    const { offers, loading } = useRestaurantData();

    useEffect(() => {
        window.scrollTo(0, 0);

        // Page Entry Animation
        gsap.fromTo('.offers-entry',
            { opacity: 0, y: 30 },
            { opacity: 1, y: 0, duration: 0.8, stagger: 0.1, ease: 'power2.out' }
        );

        // Stagger-in animation for cards
        gsap.fromTo('.offer-card-anim',
            { opacity: 0, scale: 0.9, y: 50 },
            {
                opacity: 1,
                scale: 1,
                y: 0,
                duration: 0.8,
                stagger: 0.2,
                ease: 'back.out(1.7)',
                scrollTrigger: {
                    trigger: '.offers-grid',
                    start: 'top 80%',
                }
            }
        );
    }, []);

    return (
        <div className="pt-32 pb-20 container mx-auto px-6 min-h-screen">
            {/* Header Section */}
            <div className="mb-16">
                <button
                    onClick={() => navigate('/')}
                    className="offers-entry flex items-center gap-2 font-bold mb-8 text-cafe-text/40 dark:text-cafe-text-dark/40 hover:text-cafe-primary transition-colors group"
                >
                    <ChevronLeft size={18} /> Back to Home
                </button>

                <div className="offers-entry flex items-center gap-3 px-4 py-2 bg-cafe-primary/10 text-cafe-primary border border-cafe-primary/20 rounded-full w-fit mb-6 font-bold text-xs uppercase tracking-widest">
                    <Sparkles size={14} />
                    Exclusive Deals
                </div>

                <h1 className="offers-entry text-6xl md:text-7xl font-black dark:text-cafe-text-dark tracking-tighter leading-tight mb-4">
                    Unwrap Your <br />
                    <span className="text-cafe-primary">Luxe Perks</span>
                </h1>
                <p className="offers-entry text-lg opacity-60 max-w-2xl leading-relaxed">
                    Discover our curated selection of seasonal offers and daily specials. Handcrafted treats at prices that make you smile.
                </p>
            </div>

            {/* Offers Grid */}
            <div className="offers-grid grid grid-cols-1 md:grid-cols-2 gap-10">
                {offers && offers.map((offer) => (
                    <div key={offer.id} className="offer-card-anim h-full">
                        <OfferCard offer={offer} />
                    </div>
                ))}
            </div>

            {/* Bottom CTA UI only */}
            <div className="offers-entry mt-24 text-center p-12 bg-cafe-secondary/20 dark:bg-zinc-900/50 rounded-[3rem] border border-cafe-primary/5">
                <h3 className="text-3xl font-bold mb-4 dark:text-cafe-text-dark">Want more exclusive deals?</h3>
                <p className="opacity-60 mb-8 max-w-md mx-auto">Join our LuxeRewards program and get notified about new offers before anyone else.</p>
                <button className="px-12 py-5 bg-cafe-primary text-white font-bold rounded-2xl hover:scale-105 active:scale-95 transition-transform shadow-xl shadow-cafe-primary/20">
                    Join LuxeRewards
                </button>
            </div>
        </div>
    );
};

export default OffersPage;
