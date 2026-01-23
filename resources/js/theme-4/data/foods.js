export const foods = [
    {
        id: 1,
        categoryId: 'coffee',
        name: 'Signature Latte',
        price: 4.50,
        rating: 4.9,
        image: 'https://images.unsplash.com/photo-1541167760496-162955ed8a9f?auto=format&fit=crop&q=80&w=600',
        description: 'A smooth blend of espresso and steamed milk, topped with a delicate layer of foam.',
        ingredients: ['Espresso', 'Whole Milk', 'Vanilla Syrup (Optional)'],
        bestSelling: true
    },
    {
        id: 2,
        categoryId: 'coffee',
        name: 'Cold Brew',
        price: 5.00,
        rating: 4.8,
        image: 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&q=80&w=600',
        description: 'Steeped for 16 hours for a smooth, low-acid coffee experience.',
        ingredients: ['Premium Beans', 'Filtered Water'],
        bestSelling: true
    },
    {
        id: 3,
        categoryId: 'cakes',
        name: 'Butter Croissant',
        price: 3.75,
        rating: 4.7,
        image: 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?auto=format&fit=crop&q=80&w=600',
        description: 'Flaky, buttery layers that melt in your mouth. Baked fresh daily.',
        ingredients: ['French Butter', 'Organic Flour', 'Yeast'],
        bestSelling: true
    },
    {
        id: 4,
        categoryId: 'main-course',
        name: 'Acai Power Bowl',
        price: 12.50,
        rating: 4.9,
        image: 'https://images.unsplash.com/photo-1494390248081-4e521a5940db?auto=format&fit=crop&q=80&w=600',
        description: 'Fresh acai topped with granola, seasonal berries, and a drizzle of honey.',
        ingredients: ['Acai Berry', 'Granola', 'Mixed Berries', 'Honey'],
        bestSelling: false
    },
    {
        id: 5,
        categoryId: 'cakes',
        name: 'Chocolate Lava Cake',
        price: 8.50,
        rating: 5.0,
        image: 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?auto=format&fit=crop&q=80&w=600',
        description: 'Warm chocolate cake with a molten center of rich dark chocolate.',
        ingredients: ['Belgian Chocolate', 'Cocoa Powder', 'Eggs', 'Butter'],
        bestSelling: true
    },
    {
        id: 6,
        categoryId: 'coffee',
        name: 'Cappuccino',
        price: 4.25,
        rating: 4.6,
        image: 'https://images.unsplash.com/photo-1534778101976-62847782c213?auto=format&fit=crop&q=80&w=600',
        description: 'Equal parts espresso, steamed milk, and foam for a classic Italian taste.',
        ingredients: ['Espresso', 'Steamed Milk', 'Foam'],
        bestSelling: false
    },
    {
        id: 7,
        categoryId: 'starter',
        name: 'Paneer Tikka',
        price: 12.00,
        rating: 4.9,
        image: 'https://images.unsplash.com/photo-1599487488170-d11ec9c172f0?auto=format&fit=crop&q=80&w=600',
        description: 'Marinated paneer cubes grilled to perfection with bell peppers.',
        ingredients: ['Paneer', 'Yogurt', 'Indian Spices'],
        bestSelling: true
    }
];
export const offers = [
    { id: 'off1', name: 'Buy 1 Get 1', description: 'Available on all coffee items.' },
    { id: 'off2', name: '20% OFF', description: 'On orders above $50.' }
];

export const specials = [
    {
        id: 'spec1',
        name: 'Truffle Honey Latte',
        price: 18.00,
        rating: 5.0,
        image: 'https://images.unsplash.com/photo-1600093480536-54f3b17409c9?auto=format&fit=crop&q=80&w=800',
        description: 'An exquisite blend of wild-harvested truffle essence and premium organic honey, stirred into our signature triple-shot espresso.',
        ingredients: ['Truffle Essence', 'Organic Honey', 'Triple-shot Espresso', 'Gold Flakes'],
        tag: 'NEW ARRIVAL'
    },
    {
        id: 'spec2',
        name: 'Royal Saffron Brew',
        price: 15.50,
        rating: 4.9,
        image: 'https://images.unsplash.com/photo-1544787210-2213d84ad96b?auto=format&fit=crop&q=80&w=800',
        description: 'A rich, aromatic coffee infused with hand-picked saffron strands and a hint of cardamom.',
        ingredients: ['Kashmiri Saffron', 'Cardamom', 'Dark Roast Coffee', 'Condensed Milk'],
        tag: 'CHEF\'S CHOICE'
    },
    {
        id: 'spec3',
        name: 'Velvet Rose Mocha',
        price: 14.00,
        rating: 4.8,
        image: 'https://images.unsplash.com/photo-1534778101976-62847782c213?auto=format&fit=crop&q=80&w=800',
        description: 'Classic mocha with a romantic twist of organic rose water and edible rose petals.',
        ingredients: ['Rose Water', 'Dark Chocolate', 'Espresso', 'Rose Petals'],
        tag: 'SEASONAL'
    }
];
