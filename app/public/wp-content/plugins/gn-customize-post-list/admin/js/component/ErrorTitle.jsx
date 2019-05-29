import{ Component } from 'react';

const ErrorText = (props) => {
  const {title, className} = props.errors;
  return <div className={"gncpl-admin-errorTitle " + className}>{title}</div>
}

export default ErrorText;