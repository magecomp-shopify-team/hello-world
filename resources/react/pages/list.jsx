import { Card } from '@shopify/polaris';
import React, { useEffect, useState } from 'react';

function MyComponent() {
    const [products, setProducts] = useState(null);

    useEffect(() => {
        fetch('https://manually-gentleman-prior-flour.trycloudflare.com/api/get', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                setProducts(data);
                console.log("Fetched products:", data);
            })
            .catch(error => console.error('Fetch error:', error));
    }, []);

    return (
        <Card>
            <h1>Fetched Products:</h1>
            <pre>{JSON.stringify(products, null, 2)}</pre>
        </Card>
    );
}

export default MyComponent;
