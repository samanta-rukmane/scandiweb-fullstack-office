import React, { useState } from "react";
import { Routes, Route } from "react-router-dom";

import Header from "./components/Header";
import ProductList from "./components/ProductList";
import ProductDetails from "./components/ProductDetails";
import CartOverlay from "./components/CartOverlay";

export default function App() {
  const [cartItems, setCartItems] = useState([]);
  const [isCartOpen, setIsCartOpen] = useState(false);

  const addToCart = (product) => {
    setCartItems((prev) => {
      const existing = prev.find(
        (item) =>
          item.product.id === product.id &&
          JSON.stringify(item.product.selectedAttributes) ===
            JSON.stringify(product.selectedAttributes)
      );

      if (existing) {
        return prev.map((item) =>
          item === existing
            ? { ...item, quantity: item.quantity + 1 }
            : item
        );
      }

      return [...prev, { product, quantity: 1 }];
    });

    setIsCartOpen(true);
  };

  const changeQuantity = (productId, delta) => {
    setCartItems((prev) =>
      prev
        .map((item) =>
          item.product.id === productId
            ? { ...item, quantity: item.quantity + delta }
            : item
        )
        .filter((item) => item.quantity > 0)
    );
  };

  const clearCart = () => setCartItems([]);

  const total = cartItems.reduce(
    (sum, item) => sum + item.product.price * item.quantity,
    0
  );

  const totalItems = cartItems.reduce(
    (sum, item) => sum + item.quantity,
    0
  );

  return (
    <>
      <Header
        cartItems={cartItems}
        totalItems={totalItems}
        openCart={() => setIsCartOpen(true)}
      />

      <Routes>
        <Route
          path="/"
          element={<ProductList addToCart={addToCart} />}
        />

        <Route
          path="/category/:slug"
          element={<ProductList addToCart={addToCart} />}
        />

        <Route
          path="/product/:id"
          element={<ProductDetails addToCart={addToCart} />}
        />
      </Routes>

      {isCartOpen && (
        <CartOverlay
          cartItems={cartItems}
          changeQuantity={changeQuantity}
          total={total}
          clearCart={clearCart}
          onClose={() => setIsCartOpen(false)}
        />
      )}
    </>
  );
}