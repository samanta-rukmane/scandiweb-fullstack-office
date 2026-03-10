import React, { useEffect, useState } from "react";
import { useParams, Link } from "react-router-dom";
import ProductCard from "./ProductCard";

export default function ProductList({ addToCart }) {
  const { slug } = useParams();
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  useEffect(() => {
    const categorySlug = slug || "all";

    const fetchProducts = async () => {
      try {
        setLoading(true);
        setError(null);

        const query =
          categorySlug === "all"
            ? `
            {
              products {
                id
                name
                price
                gallery
                inStock
                brand
                attributes { name type items { value displayValue } }
              }
            }`
            : `
            {
              productsByCategory(category: "${categorySlug}") {
                id
                name
                price
                gallery
                inStock
                brand
                attributes { name type items { value displayValue } }
              }
            }`;

        const response = await fetch("http://localhost:8000/graphql", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ query }),
        });

        const result = await response.json();

        if (result.errors) {
          console.error("GraphQL errors:", result.errors);
          throw new Error("Failed to fetch products");
        }

        const fetchedProducts =
          categorySlug === "all"
            ? result.data.products
            : result.data.productsByCategory;

        setProducts(fetchedProducts || []);
      } catch (err) {
        console.error(err);
        setError("Something went wrong while loading products.");
      } finally {
        setLoading(false);
      }
    };

    fetchProducts();
  }, [slug]);

  if (loading) return <div className="products-state text-center py-20 text-gray-500"> Loading products... </div>;
  if (error) return <div className="products-state text-center py-20 text-red-500">{error}</div>;
  if (!products.length) return <div className="products-state text-center py-20 text-gray-500"> No products found. </div>;

  return (
    <div className="product-list grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      {products.map(product => (
        <ProductCard key={product.id} product={product} addToCart={addToCart} />
      ))}
    </div>
  );
}