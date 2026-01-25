import React from 'react';
import { Coffee, MapPin, Clock, Instagram, Twitter, Facebook } from 'lucide-react';
import { useRestaurantData } from '../../hooks/useRestaurantData';

const Footer = () => {
    const { restaurant, opening_hours } = useRestaurantData();

    if (!restaurant) return null;

    const renderSocials = () => {
        if (!restaurant.social_links) return null;
        const { instagram, twitter, facebook } = restaurant.social_links;

        return (
            <div className="flex gap-4">
                {instagram && (
                    <a href={`https://${instagram}`} target="_blank" rel="noopener noreferrer" className="p-3 bg-white dark:bg-zinc-800 rounded-full shadow-sm hover:bg-cafe-primary hover:text-white transition-all duration-300">
                        <Instagram size={18} />
                    </a>
                )}
                {twitter && (
                    <a href={`https://${twitter}`} target="_blank" rel="noopener noreferrer" className="p-3 bg-white dark:bg-zinc-800 rounded-full shadow-sm hover:bg-cafe-primary hover:text-white transition-all duration-300">
                        <Twitter size={18} />
                    </a>
                )}
                {facebook && (
                    <a href={`https://${facebook}`} target="_blank" rel="noopener noreferrer" className="p-3 bg-white dark:bg-zinc-800 rounded-full shadow-sm hover:bg-cafe-primary hover:text-white transition-all duration-300">
                        <Facebook size={18} />
                    </a>
                )}
            </div>
        );
    };

    return (
        <footer className="bg-cafe-secondary/30 dark:bg-zinc-900 overflow-hidden pt-16 pb-8">
            <div className="container mx-auto px-6">
                <div className="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                    {/* Brand */}
                    <div className="col-span-1 md:col-span-1">
                        <div className="flex items-center gap-2 mb-6">
                            {restaurant.logo ? (
                                <img src={restaurant.logo} alt={restaurant.name} className="h-8 w-auto object-contain" />
                            ) : (
                                <div className="p-2 bg-cafe-primary rounded-lg">
                                    <Coffee className="text-white" size={20} />
                                </div>
                            )}
                            <span className="text-xl font-bold dark:text-cafe-text-dark">{restaurant.name}</span>
                        </div>
                        <p className="text-sm opacity-70 leading-relaxed">
                            {restaurant.overview || "Brewing moments of happiness. Our passion for food and artisanal treats brings people together."}
                        </p>
                    </div>

                    {/* Opening Times */}
                    <div>
                        <h4 className="font-bold mb-6 flex items-center gap-2">
                            <Clock size={16} className="text-cafe-primary" />
                            Opening Hours
                        </h4>
                        <ul className="text-sm space-y-3 opacity-80">
                            {opening_hours && opening_hours.length > 0 ? (
                                opening_hours.map((slot, i) => (
                                    <li key={i} className="flex justify-between">
                                        <span>{slot.day}</span>
                                        <span className="font-medium text-cafe-primary">{slot.open} - {slot.close}</span>
                                    </li>
                                ))
                            ) : (
                                <li>No opening hours available</li>
                            )}
                        </ul>
                    </div>

                    {/* Location */}
                    <div>
                        <h4 className="font-bold mb-6 flex items-center gap-2">
                            <MapPin size={16} className="text-cafe-primary" />
                            Location
                        </h4>
                        <p className="text-sm opacity-80 leading-relaxed mb-4">
                            {restaurant.address} {restaurant.city && `, ${restaurant.city}`}
                            {restaurant.zip_code && ` ${restaurant.zip_code}`}
                        </p>
                        <p className="text-sm opacity-80">{restaurant.phone}</p>
                    </div>

                    {/* Socials */}
                    <div>
                        <h4 className="font-bold mb-6">Follow Us</h4>
                        {renderSocials()}
                    </div>
                </div>

                <div className="border-t border-cafe-text/10 dark:border-white/10 pt-8 flex flex-col md:row justify-between items-center text-xs opacity-50">
                    <p>© {new Date().getFullYear()} {restaurant.name}. All rights reserved.</p>
                    <div className="flex gap-6 mt-4 md:mt-0">
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms of Service</a>
                    </div>
                </div>
            </div>
        </footer>
    );
};

export default Footer;
