import React, { useRef } from 'react';
import { Tag, ArrowRight } from 'lucide-react';
import gsap from 'gsap';

const OfferCard = ({ offer }) => {
    const cardRef = useRef(null);
    const buttonRef = useRef(null);

    const onMouseEnter = () => {
        gsap.to(cardRef.current, {
            y: -12,
            scale: 1.02,
            duration: 0.4,
            ease: 'power2.out',
            boxShadow: '0 25px 50px -12px rgba(212, 163, 115, 0.2)'
        });
        gsap.to(buttonRef.current, {
            x: 5,
            duration: 0.3,
            ease: 'power2.out'
        });
    };

    const onMouseLeave = () => {
        gsap.to(cardRef.current, {
            y: 0,
            scale: 1,
            duration: 0.4,
            ease: 'power2.inOut',
            boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)'
        });
        gsap.to(buttonRef.current, {
            x: 0,
            duration: 0.3,
            ease: 'power2.inOut'
        });
    };

    const [imageError, setImageError] = React.useState(false);

    return (
        <div
            ref={cardRef}
            onMouseEnter={onMouseEnter}
            onMouseLeave={onMouseLeave}
            className="bg-white dark:bg-zinc-800 rounded-[2rem] overflow-hidden shadow-sm border border-transparent hover:border-cafe-primary/20 transition-colors duration-300 group flex flex-col h-full"
        >
            <div className="relative h-48 overflow-hidden bg-zinc-100 dark:bg-zinc-900">
                {offer.image && !imageError ? (
                    <img
                        src={offer.image}
                        alt={offer.title}
                        className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                        onError={() => setImageError(true)}
                    />
                ) : (
                    <div className="w-full h-full flex items-center justify-center opacity-60">
                        <span className="text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-[0.2em] text-[10px]">No Image</span>
                    </div>
                )}
                <div className="absolute top-4 left-4 px-4 py-1.5 bg-cafe-primary/90 backdrop-blur-md text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-full flex items-center gap-2">
                    <Tag size={12} />
                    {offer.tag}
                </div>
            </div>

            <div className="p-8 flex-grow flex flex-col justify-between">
                <div>
                    <h3 className="text-2xl font-black mb-3 dark:text-cafe-text-dark tracking-tighter leading-tight">
                        {offer.title}
                    </h3>
                    <p className="text-sm opacity-60 leading-relaxed mb-8">
                        {offer.description}
                    </p>
                </div>

                <button className="flex items-center gap-3 font-bold text-sm uppercase tracking-widest text-cafe-primary hover:text-cafe-text dark:hover:text-cafe-text-dark transition-colors group/btn">
                    Claim Now
                    <span ref={buttonRef} className="p-2 bg-cafe-primary/10 rounded-full group-hover/btn:bg-cafe-primary group-hover/btn:text-white transition-all">
                        <ArrowRight size={16} />
                    </span>
                </button>
            </div>
        </div>
    );
};

export default OfferCard;
