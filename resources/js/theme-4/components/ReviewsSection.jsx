import React, { useRef } from 'react';
import { Star, User } from 'lucide-react';
import { gsap } from 'gsap';

const ReviewsSection = ({ reviews }) => {
    const scrollRef = useRef(null);

    if (!reviews || reviews.length === 0) return null;

    const scroll = (direction) => {
        if (scrollRef.current) {
            const { current } = scrollRef;
            const scrollAmount = 300;
            current.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
        }
    };

    return (
        <section className="py-20 bg-cafe-secondary/10 dark:bg-zinc-900/30">
            <div className="container mx-auto px-6">
                <div className="flex justify-between items-end mb-12">
                    <div>
                        <h2 className="text-4xl font-bold mb-2 dark:text-cafe-text-dark tracking-tight">Customer Reviews</h2>
                        <p className="opacity-60">What our guests say about us.</p>
                    </div>
                </div>

                <div className="relative group/carousel">
                    {/* Controls */}
                    {reviews.length > 3 && (
                        <>
                            <button
                                onClick={() => scroll(-1)}
                                className="absolute left-0 top-1/2 -translate-y-1/2 -ml-4 z-10 p-3 rounded-full bg-white dark:bg-zinc-800 shadow-lg opacity-0 group-hover/carousel:opacity-100 transition-opacity disabled:opacity-0"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m15 18-6-6 6-6" /></svg>
                            </button>
                            <button
                                onClick={() => scroll(1)}
                                className="absolute right-0 top-1/2 -translate-y-1/2 -mr-4 z-10 p-3 rounded-full bg-white dark:bg-zinc-800 shadow-lg opacity-0 group-hover/carousel:opacity-100 transition-opacity disabled:opacity-0"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m9 18 6-6-6-6" /></svg>
                            </button>
                        </>
                    )}

                    <div
                        ref={scrollRef}
                        className="flex gap-6 overflow-x-auto pb-4 hide-scrollbar snap-x snap-mandatory"
                        style={{ scrollbarWidth: 'none', msOverflowStyle: 'none' }}
                    >
                        {reviews.map((review) => (
                            <div
                                key={review.id}
                                className="min-w-[300px] md:min-w-[350px] bg-white dark:bg-black/40 p-6 rounded-2xl shadow-sm border border-black/5 dark:border-white/5 snap-start flex flex-col"
                            >
                                <div className="flex items-center gap-4 mb-4">
                                    {review.profile_photo_url ? (
                                        <img
                                            src={review.profile_photo_url}
                                            alt={review.author_name}
                                            className="w-12 h-12 rounded-full object-cover"
                                        />
                                    ) : (
                                        <div className="w-12 h-12 rounded-full bg-cafe-primary/20 flex items-center justify-center text-cafe-primary">
                                            <User size={24} />
                                        </div>
                                    )}
                                    <div>
                                        <h4 className="font-bold text-sm dark:text-cafe-text-dark">{review.author_name}</h4>
                                        <div className="flex text-yellow-400 gap-0.5">
                                            {[...Array(5)].map((_, i) => (
                                                <Star
                                                    key={i}
                                                    size={12}
                                                    fill={i < review.rating ? "currentColor" : "none"}
                                                    className={i < review.rating ? "" : "text-gray-300 dark:text-gray-600"}
                                                />
                                            ))}
                                        </div>
                                    </div>
                                    <span className="ml-auto text-xs text-gray-400">{review.relative_time_description}</span>
                                </div>
                                <p className="text-gray-600 dark:text-gray-300 text-sm leading-relaxed line-clamp-4">
                                    {review.text}
                                </p>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
};

export default ReviewsSection;
