 import { Route, Routes } from "react-router-dom";
	import ListPage from "./pages/list";
	import React from "react";
	import IndexPage from "./pages";
    import Tabpage from"./pages/tab";


	const AppRoute = () => {
	    return (
		<Routes>
		    <Route path="/" element={<IndexPage/>} />
		    <Route path="/list" element={<ListPage/>} />
            <Route path="/tab" element={<Tabpage/>} />
		</Routes>
	    );
	};

	export default AppRoute;