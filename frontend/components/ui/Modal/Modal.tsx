import React, { Children } from "react";
import { useState } from "react";
import "./Modal.css";

export default function Modal({
    data, children,
  }: Readonly<{
    children: React.ReactNode;
  }>) {
    const [modal, setModal] = useState(false);



    const toggleModal = () => {
        setModal(!modal);
    };

    if(modal){
        document.body.classList.add("active-modal");
    }
    else{
        document.body.classList.remove("active-modal");
    }

    return (
        <>
            <div
                onClick={toggleModal}
                className="project-card">
                    { children }
            </div>

            {modal && (

                <div className="modal">
                    <div className="overlay" onClick={toggleModal}></div>
                    <div className="modal-content">
                        {data}
                        <button className="close-modal" onClick={toggleModal}>Close</button>
                    </div>
                </div>
            )}
        </>

    );
}
