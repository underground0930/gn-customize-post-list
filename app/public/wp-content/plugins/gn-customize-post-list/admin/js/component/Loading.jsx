import React, { Component } from 'react';
import ReactDOM from 'react-dom';

class Loading extends Component{
  constructor(props){
    super(props);    
    this.rootEl = document.querySelector('.gncpl-root');
  }
  render(){
    return (
      ReactDOM.createPortal(
        <div className="gncpl-admin-loading">
          <div className="gncpl-admin-loadingObj"></div>
        </div>,
        this.rootEl
      )
    );
  }
}

export default Loading;