import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import parse from "html-react-parser";

export default function ProductDetails({ addToCart }) {
  const { id } = useParams();
  const productId = id;

  const [product, setProduct] = useState(null);
  const [loading, setLoading] = useState(true);
  const [mainImage, setMainImage] = useState("");
  const [selectedAttributes, setSelectedAttributes] = useState({});

  useEffect(() => {
    if (!productId) return;

    const fetchProduct = async () => {
      try {
        setLoading(true);

        const query = `
        {
          product(id: "${productId}"){
            id
            name
            brand
            inStock
            description
            gallery
            price
            attributes {
              name
              type
              items {
                value
                displayValue
              }
            }
          }
        }`;

        const response = await fetch("http://localhost:8000/graphql", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ query }),
        });

        const result = await response.json();

        if (result.errors) {
          console.error("GraphQL errors full:", JSON.stringify(result.errors, null, 2));
        }
        console.log("GraphQL data full:", JSON.stringify(result.data, null, 2));

        const fetchedProduct = result?.data?.product;

        setProduct(fetchedProduct);
        setMainImage(fetchedProduct?.gallery?.[0] || "");
      } catch (error) {
        console.error(error);
      } finally {
        setLoading(false);
      }
    };

    fetchProduct();
  }, [productId]);

  if (loading)
    return <div className="text-center py-20 text-gray-500"> Loading product... </div>;
  if (!product)
    return <div className="text-center py-20 text-red-500"> Product not found </div>;

  const handleSelect = (attrName, value) => {
    setSelectedAttributes((prev) => ({ ...prev, [attrName]: value }));
  };

  const attributes = product.attributes || [];
  const allSelected = attributes.every((attr) => selectedAttributes[attr.name]);

  return (
    <div className="product-page">
      <div className="product-gallery">
        {product.gallery.map((img) => (
          <img
            key={img}
            src={img}
            alt={product.name}
            className={`gallery-thumbnail ${mainImage === img ? 'border-2 border-black' : ''}`}
            onClick={() => setMainImage(img)}
          />
        ))}
      </div>

      <div>
        <img
          src={mainImage}
          alt={product.name}
          className="product-main-image"
        />
      </div>

      <div>
        <h2 className="text-2xl font-semibold">{product.brand}</h2>
        <h3 className="text-xl mb-6">{product.name}</h3>

        {attributes.map((attr) => (
          <div key={attr.name} className="attribute-container">
            <strong className="attribute-label">{attr.name}:</strong>
            <div className="flex gap-2">
              {attr.type === "swatch"
                ? attr.items.map((item) => (
                    <button
                      key={item.value}
                      style={{ backgroundColor: item.value }}
                      className={`color-swatch ${selectedAttributes[attr.name] === item.value ? 'selected' : ''}`}
                      onClick={() => handleSelect(attr.name, item.value)}
                    />
                  ))
                : attr.items.map((item) => (
                    <button
                      key={item.value}
                      className={`attribute-btn ${selectedAttributes[attr.name] === item.value ? 'selected' : ''}`}
                      onClick={() => handleSelect(attr.name, item.value)}
                    >
                      {item.displayValue || item.value}
                    </button>
                  ))
              }
            </div>
          </div>
        ))}

        <p className="price-label">PRICE:</p>
        <p className="price-value">${parseFloat(product.price).toFixed(2)}</p>

        <button
          disabled={!allSelected || !product.inStock}
          className="add-to-cart-btn"
          onClick={() => addToCart({ ...product, selectedAttributes })}
        >
          ADD TO CART
        </button>

        <div className="mt-6 text-sm">
          {parse(product.description)}
        </div>
      </div>
    </div>
  );
}