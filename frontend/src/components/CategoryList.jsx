import React, { useEffect, useState } from "react";
import { Link, useLocation } from "react-router-dom";

export default function CategoryList() {
  const [categories, setCategories] = useState([]);
  const location = useLocation();

  const currentSlug =
    location.state?.category ||
    (location.pathname.startsWith("/category/")
      ? location.pathname.replace("/category/", "")
      : "all");

  useEffect(() => {
    fetch("http://localhost:8000/graphql", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ query: `{ categories { id name slug } }` }),
    })
      .then(res => res.json())
      .then(result => setCategories(result?.data?.categories || []))
      .catch(console.error);
  }, []);

  return (
    <nav className="category-nav">
      {categories.map((c) => {
        const isActive = currentSlug === c.slug;
        return (
          <Link
            key={c.id}
            to={`/category/${c.slug}`}
            className={isActive ? "active-category" : ""}
          >
            {c.name.toUpperCase()}
          </Link>
        );
      })}
    </nav>
  );
}