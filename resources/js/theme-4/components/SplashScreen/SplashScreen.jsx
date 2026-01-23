import React, { useEffect, useRef } from 'react';
import gsap from 'gsap';

const SplashScreen = ({ onFinish }) => {
    const containerRef = useRef(null);
    const logoRef = useRef(null);
    const textRef = useRef(null);

    useEffect(() => {
        const tl = gsap.timeline({
            onComplete: () => {
                gsap.to(containerRef.current, {
                    opacity: 0,
                    duration: 0.8,
                    ease: 'power2.inOut',
                    onComplete: onFinish
                });
            }
        });

        tl.fromTo(logoRef.current,
            { scale: 0.5, opacity: 0 },
            { scale: 1.2, opacity: 1, duration: 1.2, ease: 'back.out(1.7)' }
        )
            .fromTo(textRef.current,
                { y: 20, opacity: 0 },
                { y: 0, opacity: 1, duration: 0.8, ease: 'power2.out' },
                "-=0.5"
            )
            .to(logoRef.current, {
                scale: 1,
                duration: 1,
                ease: 'power2.inOut'
            }, "+=0.5");

        return () => tl.kill();
    }, [onFinish]);

    return (
        <div
            ref={containerRef}
            className="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-cafe-light dark:bg-cafe-dark"
        >
            <div ref={logoRef} className="mb-6">
                <div className="w-24 h-24 bg-cafe-primary rounded-full flex items-center justify-center shadow-2xl">
                    <span className="text-4xl text-white font-bold">C</span>
                </div>
            </div>
            <h1
                ref={textRef}
                className="text-3xl font-bold tracking-widest text-cafe-text dark:text-cafe-text-dark uppercase"
            >
                Luxe Cafe
            </h1>
            <div className="mt-8 flex gap-2">
                {[0, 1, 2].map(i => (
                    <div key={i} className="w-2 h-2 bg-cafe-primary rounded-full animate-bounce" style={{ animationDelay: `${i * 0.2}s` }}></div>
                ))}
            </div>
        </div>
    );
};

export default SplashScreen;
