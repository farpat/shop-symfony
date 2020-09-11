import React, { useEffect, useRef, useState } from 'react'
import PropTypes from 'prop-types'

function ReferenceSliderComponent ({ currentReference }) {
  const [activatedIndex, setActivatedIndex] = useState(0)
  const carousel = useRef(null)

  const getId = function () {
    return `carousel-reference-${currentReference.id}`
  }

  useEffect(() => {
    setActivatedIndex(0)

    const updateActivatedIndex = function (event) {
      event.preventDefault()
      setActivatedIndex(event.to)
    }

    carousel.current.addEventListener('slid.bs.carousel', updateActivatedIndex)

    return () => carousel.current.removeEventListener('slid.bs.carousel', updateActivatedIndex)
  }, [currentReference])

  return (
    <>
      <div id={getId()} className='carousel slide carousel-fade carousel-product' data-ride='carousel' ref={carousel}>
        <div className='carousel-inner'>
          {
            currentReference.images.map((image, index) =>
              <div key={image.id} className={'carousel-item' + (index === activatedIndex ? ' active' : '')}>
                <img src={image.url} alt={image.alt}/>
              </div>
            )
          }
        </div>

        {
          currentReference.images.length > 1 &&
          <>
            <a href={'#' + getId()} className='carousel-control-prev' data-slide='prev' role='button'>
              <span aria-hidden='true' className='carousel-control-prev-icon'/>
              <span className='sr-only'>Previous</span>
            </a>
            <a href={'#' + getId()} className='carousel-control-next' data-slide='next' role='button'>
              <span aria-hidden='true' className='carousel-control-next-icon'/>
              <span className='sr-only'>Next</span>
            </a>
          </>
        }
      </div>
      {
        currentReference.images.length > 1 &&
        <ol className='carousel-indicators carousel-product-indicators'>
          {
            currentReference.images.map((image, index) =>
              <li className={activatedIndex === index ? 'active' : ''} data-slide-to={index} data-target={'#' + getId()}
                  key={index}>
                <img src={image.url_thumbnail} alt={image.alt_thumbnail}/>
              </li>
            )
          }
        </ol>
      }
    </>
  )
}

ReferenceSliderComponent.propTypes = {
  currentReference: PropTypes.shape({
    id                     : PropTypes.number.isRequired,
    images                 : PropTypes.arrayOf(
      PropTypes.shape({
        id           : PropTypes.number.isRequired,
        url          : PropTypes.string.isRequired,
        alt          : PropTypes.string.isRequired,
        url_thumbnail: PropTypes.string.isRequired,
        alt_thumbnail: PropTypes.string.isRequired
      })
    )
  }),
}

export default ReferenceSliderComponent
